<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembangunan;
use App\Models\Proyek;
use App\Models\Satuan;
use App\Models\Toko;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenguruganExport;
use Carbon\Carbon;
use DateTime;

class PenguruganController extends Controller
{
    public function index() {
        $pengurugan = Pembangunan::where('ket', 'pengeluaran urug')->orderBy('tanggal')->orderBy('nama')->get();
        $periodes = Pembangunan::where('ket', 'pengeluaran urug')
            ->select(Pembangunan::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        $proyek = Proyek::orderBy('nama')->get();
        $namaProyek = Proyek::pluck('nama', 'id_proyek');
        $satuan = Satuan::orderBy('nama')->get();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        $toko = Toko::orderBy('nama')->get();
        $namaToko = Toko::pluck('nama', 'id_toko');
        return view('contents.pembangunan.pengurugan', compact('pengurugan', 'proyek', 'namaProyek', 'satuan', 'namaSatuan', 'toko', 'namaToko', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        // File
        $request->validate([
            'file' => 'mimes:pdf,png,jpeg,jpg|max:2048',
        ]);

        $filePath = null;
        if ($request->file('file')) {
            $file = $request->file('file');
            $dateTime = new DateTime();
            $dateTime->modify('+7 hours');
            $currentDateTime = $dateTime->format('d_m_Y_H_i_s');
            $fileName = $currentDateTime . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('Urug', $fileName, 'public');
        }

        $dataUrug = [
            'ket' => 'pengeluaran urug',
            'id_proyek' => $request->proyek,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'ukuran' => $request->ukuran,
            'jumlah' => $request->jumlah,
            'id_satuan' => $request->satuan,
            'harga' => $numericHarga,
            'tot_harga' => $numericTotal,
            'id_toko' => $request->toko,
            'file' => $filePath
        ];

        $dataProyek = Proyek::where('id_proyek', $request->proyek)->first();
        $namaProyek = 'null';
        if ($dataProyek) {
            $namaProyek = $dataProyek->nama;
        } 

        $dataToko = Toko::where('id_toko', $request->toko)->first();
        $namaToko = 'null';
        if ($dataToko) {
            $namaToko = $dataToko->nama;
        }

        $exitingUrug = Pembangunan::where('tanggal', $request->tanggal)
            ->where('nama', $request->nama)
            ->where('deskripsi', $request->deskripsi)
            ->where('ukuran', $request->ukuran)
            ->where('jumlah', $request->jumlah)
            ->where('id_satuan', $request->satuan)
            ->where('harga', $numericHarga)
            ->where('tot_harga', $numericTotal)
            ->where('id_proyek', $request->proyek)
            ->where('id_toko', $request->toko)
            ->first();

        if ($exitingUrug) {
            $logErrors = 'Proyek: ' . $namaProyek . ' - Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . 
                ' - Nama: ' . $request->nama . ' - Jumlah: ' . $request->jumlah . 
                ' - Harga: ' . $request->harga . ' - Total Harga: ' . $request->total . 
                ' - Toko: ' . $namaToko . ', data tersebut sudah ada di sistem';
        
            return redirect('pengurugan')->with('logErrors', $logErrors);

        } else {
            Pembangunan::create($dataUrug);
            return redirect('pengurugan');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        // File
        $request->validate([
            'file' => 'mimes:pdf,png,jpeg,jpg|max:2048',
        ]);

        $filePath = null;
        if ($request->file('file')) {
            $file = $request->file('file');
            $dateTime = new DateTime();
            $dateTime->modify('+7 hours');
            $currentDateTime = $dateTime->format('d_m_Y_H_i_s');
            $fileName = $currentDateTime . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('Urug', $fileName, 'public');
        }
        
        $tagihanUrug = Pembangunan::find($request->id_pengurugan);
        if ($tagihanUrug) {
            $tagihanUrug->id_proyek = $request->proyek;
            $tagihanUrug->tanggal = $request->tanggal;
            $tagihanUrug->nama = $request->nama;
            $tagihanUrug->ukuran = $request->ukuran;
            $tagihanUrug->deskripsi = $request->deskripsi;
            $tagihanUrug->jumlah = $request->jumlah;
            $tagihanUrug->id_satuan = $request->satuan;
            $tagihanUrug->harga = $numericHarga;
            $tagihanUrug->tot_harga = $numericTotal;
            $tagihanUrug->id_toko = $request->toko;

            if ($filePath) {
                $tagihanUrug->file = $filePath;
            }

            $tagihanUrug->save();
            return redirect('pengurugan?page=' . $request->page)->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('pengurugan');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->delete();
        return redirect('pengurugan');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $nama = 'Pengurugan';

        // Ambil input tanggal dari request
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        $rangeDate = null;

        // Format bulan dan tahun untuk perbandingan
        $start_month_year = $start_date->format('m-Y');
        $end_month_year = $end_date->format('m-Y');

        // Format tahun untuk perbandingan
        $start_year = $start_date->format('Y');
        $end_year = $end_date->format('Y');

        if ($start_date->isSameDay($end_date)) {
            // Format tanggal yang diinginkan jika tanggal sama
            $rangeDate = $start_date->format('d M Y');

        } elseif ($start_month_year === $end_month_year) {
            // Format tanggal yang diinginkan jika bulan dan tahun sama
            $start_day = $start_date->format('d');
            $end_day = $end_date->format('d');
            $month_year = $start_date->format('M Y');
            $rangeDate = "{$start_day} - {$end_day} {$month_year}";

        } elseif ($start_year === $end_year) {
            // Format tanggal yang diinginkan jika tahun sama tetapi bulan berbeda
            $start_day = $start_date->format('d');
            $end_day = $end_date->format('d');
            $start_month = $start_date->format('M');
            $end_month = $end_date->format('M');
            $year = $start_date->format('Y');
            $rangeDate = "{$start_day} {$start_month} - {$end_day} {$end_month} {$year}";

        } else {
            $rangeDate = "{$start_date->format('d M Y')} - {$end_date->format('d M Y')}";
        }

        if ($mode == 'all_data') {
            $pengurugan = Pembangunan::where('ket', 'pengeluaran urug')->orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
            return Excel::download(new PenguruganExport($mode, $pengurugan, $nama, $rangeDate), 'Report Pengurugan.xlsx');

        } else {
            $pengurugan = Pembangunan::where('ket', 'pengeluaran urug')->where('tanggal', '>=', $start_date)
                ->where('tanggal', '<=', $end_date)
                ->orderBy('tanggal', 'asc')
                ->orderBy('nama', 'asc')
                ->get();

            $fileName = 'Report Pengurugan ' . $rangeDate . '.xlsx';
            return Excel::download(new PenguruganExport($mode, $pengurugan, $nama, $rangeDate), $fileName);
        }
    }

    // Update status
    public function pending(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->update([
            'status' => 'pending'
        ]);
        return redirect('pengurugan');
    }

    public function process(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->update([
            'status' => 'processing'
        ]);
        return redirect('pengurugan');
    }

    public function paid(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->update([
            'status' => 'paid'
        ]);
        return redirect('pengurugan');
    }
}
