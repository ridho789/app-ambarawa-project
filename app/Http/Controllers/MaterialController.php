<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembangunan;
use App\Models\Proyek;
use App\Models\Satuan;
use App\Models\Toko;
use App\Models\KategoriMaterial;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaterialExport;
use Carbon\Carbon;
use DateTime;

class MaterialController extends Controller
{
    public function index() {
        $material = Pembangunan::whereNotNull('id_kategori')
            ->where(function ($query) {
                $query->whereNull('noform')
                    ->orWhereHas('permintaanBarang', function ($query) {
                        $query->whereColumn('tbl_pembangunan.noform', 'tbl_permintaan_barang.noform')
                                ->where('status', 'approved');
                    });
            })
            ->orderBy('id_kategori')
            ->orderBy('tanggal')
            ->orderBy('nama')
            ->get();

        $periodes = Pembangunan::whereNotNull('id_kategori')
            ->select(Pembangunan::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        $proyek = Proyek::orderBy('nama')->get();
        $namaProyek = Proyek::pluck('nama', 'id_proyek');
        $satuan = Satuan::orderBy('nama')->get();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        $kategori = KategoriMaterial::orderBy('nama')->get();
        $toko = Toko::orderBy('nama')->get();
        $namaToko = Toko::pluck('nama', 'id_toko');
        return view('contents.pembangunan.kontruksi.material', compact('material', 'proyek', 'namaProyek', 'satuan', 'namaSatuan', 'toko', 'namaToko', 'kategori', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $masa_pakai = null;
        if ($request->masa && $request->waktu) {
            $masa_pakai = $request->masa . ' ' . $request->waktu;
        }

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
            $filePath = $file->storeAs('Material', $fileName, 'public');
        }

        $dataKategori = KategoriMaterial::where('id_kategori', $request->kategori)->first();
        $namaKategori = 'null';
        if ($dataKategori) {
            $namaKategori = $dataKategori->nama;
        }

        $dataMaterial = [
            'ket' => 'pengeluaran ' . strtolower($namaKategori),
            'id_proyek' => $request->proyek,
            'pemesan' => $request->pemesan,
            'kategori_barang' => $request->kategori_barang,
            'masa_pakai' => $masa_pakai,
            'no_inventaris' => $request->no_inventaris,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'id_satuan' => $request->satuan,
            'id_kategori' => $request->kategori,
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

        $existingMaterial = Pembangunan::where('ket', 'pengeluaran ' . $namaKategori)
            ->where('tanggal', $request->tanggal)
            ->where('pemesan', $request->pemesan)
            ->where('kategori_barang', $request->kategori_barang)
            ->where('masa_pakai', $masa_pakai)
            ->where('no_inventaris', $request->no_inventaris)
            ->where('nama', $request->nama)
            ->where('deskripsi', $request->deskripsi)
            ->where('jumlah', $request->jumlah)
            ->where('id_satuan', $request->satuan)
            ->where('harga', $numericHarga)
            ->where('tot_harga', $numericTotal)
            ->where('id_proyek', $request->proyek)
            ->where('id_kategori', $request->kategori)
            ->where('id_toko', $request->toko)
            ->first();

        if ($existingMaterial) {
            $logErrors = 
                'Proyek: ' . $namaProyek . ' - ' . 
                'Pengeluaran ' . $namaKategori . ' ' . 
                'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 
                'Pemesan: ' . $request->pemesan . ' - ' . 
                'Nama Barang: ' . $request->nama . ' - ' . 
                'Jumlah: ' . $request->jumlah . ' - ' . 
                'Total Harga: ' . $request->total . ' - ' . 
                'Toko: ' . $namaToko . 
                ', data tersebut sudah ada di sistem';

            return redirect('material')->with('logErrors', $logErrors);

        } else {
            Pembangunan::create($dataMaterial);
            return redirect('material');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $masa_pakai = null;
        if ($request->masa && $request->waktu) {
            $masa_pakai = $request->masa . ' ' . $request->waktu;
        }

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
            $filePath = $file->storeAs('Material', $fileName, 'public');
        }
        
        $tagihanMaterial = Pembangunan::find($request->id_material);
        if ($tagihanMaterial) {
            $tagihanMaterial->id_proyek = $request->proyek;
            $tagihanMaterial->tanggal = $request->tanggal;
            $tagihanMaterial->pemesan = $request->pemesan;
            $tagihanMaterial->kategori_barang = $request->kategori_barang;
            $tagihanMaterial->no_inventaris = $request->no_inventaris;
            $tagihanMaterial->masa_pakai = $masa_pakai;
            $tagihanMaterial->nama = $request->nama;
            $tagihanMaterial->deskripsi = $request->deskripsi;
            $tagihanMaterial->jumlah = $request->jumlah;
            $tagihanMaterial->id_satuan = $request->satuan;
            $tagihanMaterial->id_kategori = $request->kategori;
            $tagihanMaterial->harga = $numericHarga;
            $tagihanMaterial->tot_harga = $numericTotal;
            $tagihanMaterial->id_toko = $request->toko;

            if ($filePath) {
                $tagihanMaterial->file = $filePath;
            }

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
            $material = Pembangunan::whereNotNull('id_kategori')
            ->where(function ($query) {
                $query->whereNull('noform')
                    ->orWhereHas('permintaanBarang', function ($query) {
                        $query->whereColumn('tbl_pembangunan.noform', 'tbl_permintaan_barang.noform')
                                ->where('status', 'approved');
                    });
            })
            ->orderBy('id_kategori', 'asc')->orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
            return Excel::download(new MaterialExport($mode, $material, $nama, $rangeDate), 'Report Material.xlsx');

        } else {
            $material = Pembangunan::whereNotNull('id_kategori')
            ->where(function ($query) {
                $query->whereNull('noform')
                    ->orWhereHas('permintaanBarang', function ($query) {
                        $query->whereColumn('tbl_pembangunan.noform', 'tbl_permintaan_barang.noform')
                                ->where('status', 'approved');
                    });
            })
            ->where('tanggal', '>=', $start_date)
            ->where('tanggal', '<=', $end_date)
            ->orderBy('id_kategori', 'asc')
            ->orderBy('tanggal', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

            $fileName = 'Report Material ' . $rangeDate . '.xlsx';
            return Excel::download(new MaterialExport($mode, $material, $nama, $rangeDate), $fileName);
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
        return redirect('material');
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
        return redirect('material');
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
        return redirect('material');
    }
}
