<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembangunan;
use App\Models\Proyek;
use App\Models\Satuan;
use App\Models\Toko;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PembangunanExport;
use Carbon\Carbon;
use DateTime;

class BesiController extends Controller
{
    public function index() {
        $besi = Pembangunan::where('ket', 'pengeluaran besi')
            ->where(function ($query) {
                $query->whereNull('noform')
                    ->orWhereHas('permintaanBarang', function ($query) {
                        $query->whereColumn('tbl_pembangunan.noform', 'tbl_permintaan_barang.noform')
                                ->where('status', 'approved');
                    });
            })
            ->orderByRaw('tanggal IS NULL')
            ->orderBy('tanggal')
            ->orderBy('nama')
            ->get();

        $proyek = Proyek::all();
        $namaProyek = Proyek::pluck('nama', 'id_proyek');
        $satuan = Satuan::all();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        $toko = Toko::all();
        $namaToko = Toko::pluck('nama', 'id_toko');
        return view('contents.pembangunan.kontruksi.besi', compact('besi', 'proyek', 'namaProyek', 'satuan', 'toko', 'namaToko', 'namaSatuan'));
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
            $filePath = $file->storeAs('Besi', $fileName, 'public');
        }

        $dataBesi = [
            'ket' => 'pengeluaran besi',
            'id_proyek' => $request->proyek,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'ukuran' => $request->ukuran,
            'deskripsi' => $request->deskripsi,
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

        $exitingBesi = Pembangunan::where('tanggal', $request->tanggal)->where('nama', $request->nama)->where('ukuran', $request->ukuran)->where('deskripsi', $request->deskripsi)
            ->where('jumlah', $request->jumlah)->where('id_satuan', $request->satuan)->where('harga', $numericHarga)->where('tot_harga', $numericTotal)->where('id_proyek', $request->proyek)
            ->where('id_toko', $request->toko)->first();

        if ($exitingBesi) {
            $logErrors = 'Proyek: ' . $namaProyek . ' - ' . 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Nama (Barang): ' . $request->nama . ' - ' . 
            'Jumlah: ' . $request->jumlah . ' - ' . 'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . 
            ' - ' . 'Toko: ' . $namaToko . ', data tersebut sudah ada di sistem';

            return redirect('besi')->with('logErrors', $logErrors);

        } else {
            Pembangunan::create($dataBesi);
            return redirect('besi');
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
            $filePath = $file->storeAs('Besi', $fileName, 'public');
        }
        
        $tagihanBesi = Pembangunan::find($request->id_besi);
        if ($tagihanBesi) {
            $tagihanBesi->id_proyek = $request->proyek;
            $tagihanBesi->tanggal = $request->tanggal;
            $tagihanBesi->nama = $request->nama;
            $tagihanBesi->ukuran = $request->ukuran;
            $tagihanBesi->deskripsi = $request->deskripsi;
            $tagihanBesi->jumlah = $request->jumlah;
            $tagihanBesi->id_satuan = $request->satuan;
            $tagihanBesi->harga = $numericHarga;
            $tagihanBesi->tot_harga = $numericTotal;
            $tagihanBesi->id_toko = $request->toko;

            if ($filePath) {
                $tagihanBesi->file = $filePath;
            }

            $tagihanBesi->save();
            return redirect('besi')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('besi');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->delete();
        return redirect('besi');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $nama = 'Besi';

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
            $besi = Pembangunan::where('ket', 'pengeluaran besi')
            ->where(function ($query) {
                $query->whereNull('noform')
                    ->orWhereHas('permintaanBarang', function ($query) {
                        $query->whereColumn('tbl_pembangunan.noform', 'tbl_permintaan_barang.noform')
                                ->where('status', 'approved');
                    });
            })
            ->whereNotNull('tanggal')->orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();

            if (count($besi) > 0) {
                return Excel::download(new PembangunanExport($mode, $besi, $nama, $rangeDate), 'Report Besi.xlsx');
            }

            $logErrors = 'Data tidak bisa diekspor karena masih ada yang belum lengkap!';
            return redirect('besi')->with('logErrors', $logErrors);

        } else {
            $besi = Pembangunan::where('ket', 'pengeluaran besi')
            ->where(function ($query) {
                $query->whereNull('noform')
                    ->orWhereHas('permintaanBarang', function ($query) {
                        $query->whereColumn('tbl_pembangunan.noform', 'tbl_permintaan_barang.noform')
                                ->where('status', 'approved');
                    });
            })
            ->whereNotNull('tanggal')->where('tanggal', '>=', $start_date)
                ->where('tanggal', '<=', $end_date)
                ->orderBy('tanggal', 'asc')
                ->orderBy('nama', 'asc')
                ->get();

            $fileName = 'Report Besi ' . $rangeDate . '.xlsx';
            if (count($besi) > 0) {
                return Excel::download(new PembangunanExport($mode, $besi, $nama, $rangeDate), $fileName);
            }

            $logErrors = 'Data tidak bisa diekspor karena masih ada yang belum lengkap!';
            return redirect('besi')->with('logErrors', $logErrors);
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
        return redirect('besi');
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
        return redirect('besi');
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
        return redirect('besi');
    }
}
