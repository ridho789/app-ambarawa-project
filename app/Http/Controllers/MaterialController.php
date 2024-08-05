<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembangunan;
use App\Models\Proyek;
use App\Models\Satuan;
use App\Models\KategoriMaterial;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PembangunanExport;
use Carbon\Carbon;

class MaterialController extends Controller
{
    public function index() {
        $material = Pembangunan::whereNotNull('id_kategori')->orderBy('id_kategori')->orderBy('tanggal')->orderBy('nama')->get();
        $periodes = Pembangunan::whereNotNull('id_kategori')
            ->select(Pembangunan::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        $proyek = Proyek::all();
        $namaProyek = Proyek::pluck('nama', 'id_proyek');
        $satuan = Satuan::all();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        $kategori = KategoriMaterial::all();
        return view('contents.pembangunan.kontruksi.material', compact('material', 'proyek', 'namaProyek', 'satuan', 'namaSatuan', 'kategori', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataKategori = KategoriMaterial::where('id_kategori', $request->kategori)->first();
        $namaKategori = 'null';
        if ($dataKategori) {
            $namaKategori = $dataKategori->nama;
        }

        $dataMaterial = [
            'ket' => 'pengeluaran ' . strtolower($namaKategori),
            'id_proyek' => $request->proyek,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'ukuran' => $request->ukuran,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'id_satuan' => $request->satuan,
            'id_kategori' => $request->kategori,
            'harga' => $numericHarga,
            'tot_harga' => $numericTotal,
            'toko' => $request->toko,
        ];

        $dataProyek = Proyek::where('id_proyek', $request->proyek)->first();
        $namaProyek = 'null';
        if ($dataProyek) {
            $namaProyek = $dataProyek->nama;
        }

        $exitingMaterial = Pembangunan::where('tanggal', $request->tanggal)->where('nama', $request->nama)->where('ukuran', $request->ukuran)->where('deskripsi', $request->deskripsi)
            ->where('jumlah', $request->jumlah)->where('id_satuan', $request->satuan)->where('harga', $numericHarga)->where('tot_harga', $numericTotal)->where('id_proyek', $request->proyek)
            ->where('id_kategori', $request->kategori)->where('toko', $request->toko)->first();

        if ($exitingMaterial) {
            $logErrors = 'Proyek: ' . $namaProyek . ' - ' . 'Pengeluaran ' . $namaKategori . 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 
            'Nama (Barang): ' . $request->nama . ' - ' . 'Jumlah: ' . $request->jumlah . ' - ' . 'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . 
            ' - ' . 'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';

            return redirect('material')->with('logErrors', $logErrors);

        } else {
            Pembangunan::create($dataMaterial);
            return redirect('material');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanMaterial = Pembangunan::find($request->id_material);
        if ($tagihanMaterial) {
            $tagihanMaterial->id_proyek = $request->proyek;
            $tagihanMaterial->tanggal = $request->tanggal;
            $tagihanMaterial->nama = $request->nama;
            $tagihanMaterial->ukuran = $request->ukuran;
            $tagihanMaterial->deskripsi = $request->deskripsi;
            $tagihanMaterial->jumlah = $request->jumlah;
            $tagihanMaterial->id_satuan = $request->satuan;
            $tagihanMaterial->id_kategori = $request->kategori;
            $tagihanMaterial->harga = $numericHarga;
            $tagihanMaterial->tot_harga = $numericTotal;
            $tagihanMaterial->toko = $request->toko;

            $tagihanMaterial->save();
            return redirect('material')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('material');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->delete();
        return redirect('material');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $nama = 'Material';

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
            $material = Pembangunan::whereNotNull('id_kategori')->orderBy('id_kategori', 'asc')->orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
            return Excel::download(new PembangunanExport($mode, $material, $nama, $rangeDate), 'Report Material.xlsx');

        } else {
            $material = Pembangunan::whereNotNull('id_kategori')->where('tanggal', '>=', $start_date)
                ->where('tanggal', '<=', $end_date)
                ->orderBy('id_kategori', 'asc')
                ->orderBy('tanggal', 'asc')
                ->orderBy('nama', 'asc')
                ->get();

            $fileName = 'Report Material ' . $rangeDate . '.xlsx';
            return Excel::download(new PembangunanExport($mode, $material, $nama, $rangeDate), $fileName);
        }
    }
}
