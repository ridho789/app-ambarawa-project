<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembangunan;
use App\Models\Proyek;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PembangunanExport;
use Carbon\Carbon;

class BatuController extends Controller
{
    public function index() {
        $batu = Pembangunan::where('ket', 'pengeluaran batu')->orderBy('tanggal')->orderBy('nama')->get();
        $periodes = Pembangunan::where('ket', 'pengeluaran batu')
            ->select(Pembangunan::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        $proyek = Proyek::all();
        $namaProyek = Proyek::pluck('nama', 'id_proyek');
        return view('contents.pembangunan.kontruksi.batu', compact('batu', 'proyek', 'namaProyek', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataBatu = [
            'ket' => 'pengeluaran batu',
            'id_proyek' => $request->proyek,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'ukuran' => $request->ukuran,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'harga' => $numericHarga,
            'tot_harga' => $numericTotal
        ];

        $dataProyek = Proyek::where('id_proyek', $request->proyek)->first();
        $namaProyek = 'null';
        if ($dataProyek) {
            $namaProyek = $dataProyek->nama;
        } 

        $exitingBatu = Pembangunan::where('tanggal', $request->tanggal)->where('nama', $request->nama)->where('ukuran', $request->ukuran)->where('deskripsi', $request->deskripsi)
            ->where('jumlah', $request->jumlah)->where('satuan', $request->satuan)->where('harga', $numericHarga)->where('tot_harga', $numericTotal)->where('id_proyek', $request->proyek)
            ->first();

        if ($exitingBatu) {
            $logErrors = 'Proyek: ' . $namaProyek . ' - ' . 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Nama (Barang): ' . $request->nama . ' - ' . 
            'Jumlah: ' . $request->jumlah . ' - ' . 'Satuan: ' . $request->satuan . ' - ' . 'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . 
            ', data tersebut sudah ada di sistem';

            return redirect('batu')->with('logErrors', $logErrors);

        } else {
            Pembangunan::create($dataBatu);
            return redirect('batu');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanBatu = Pembangunan::find($request->id_batu);
        if ($tagihanBatu) {
            $tagihanBatu->id_proyek = $request->proyek;
            $tagihanBatu->tanggal = $request->tanggal;
            $tagihanBatu->nama = $request->nama;
            $tagihanBatu->ukuran = $request->ukuran;
            $tagihanBatu->deskripsi = $request->deskripsi;
            $tagihanBatu->jumlah = $request->jumlah;
            $tagihanBatu->satuan = $request->satuan;
            $tagihanBatu->harga = $numericHarga;
            $tagihanBatu->tot_harga = $numericTotal;

            $tagihanBatu->save();
            return redirect('batu')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('batu');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->delete();
        return redirect('batu');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $nama = 'Batu';

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
            $batu = Pembangunan::where('ket', 'pengeluaran batu')->orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
            return Excel::download(new PembangunanExport($mode, $batu, $nama, $rangeDate), 'Report Batu.xlsx');

        } else {
            $batu = Pembangunan::where('ket', 'pengeluaran batu')->where('tanggal', '>=', $start_date)
                ->where('tanggal', '<=', $end_date)
                ->orderBy('tanggal', 'asc')
                ->orderBy('nama', 'asc')
                ->get();

            $fileName = 'Report Batu ' . $rangeDate . '.xlsx';
            return Excel::download(new PembangunanExport($mode, $batu, $nama, $rangeDate), $fileName);
        }
    }
}
