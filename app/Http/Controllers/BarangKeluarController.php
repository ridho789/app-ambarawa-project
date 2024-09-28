<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use App\Models\StokBarang;
use App\Models\Kendaraan;
use App\Models\Satuan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BarangKeluarExport;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BarangKeluarController extends Controller
{
    public function index() {
        $barangKeluar = BarangKeluar::orderBy('tanggal_keluar')->orderBy('id_stok_barang')->orderBy('id_kendaraan')->get();
        $stok = StokBarang::orderBy('nama')->get();
        $namaStokBarang = StokBarang::pluck('nama', 'id_stok_barang');
        $merkStokBarang = StokBarang::pluck('merk', 'id_stok_barang');
        $typeStokBarang = StokBarang::pluck('type', 'id_stok_barang');
        $ketStokBarang = StokBarang::pluck('keterangan', 'id_stok_barang');
        $sisaStokBarang = StokBarang::pluck('jumlah', 'id_stok_barang');
        $satuanStokBarang = StokBarang::pluck('id_satuan', 'id_stok_barang');
        $kendaraan = Kendaraan::all();
        $nopolKendaraan = Kendaraan::pluck('nopol', 'id_kendaraan');
        $merkKendaraan = Kendaraan::pluck('merk', 'id_kendaraan');
        $satuan = Satuan::all();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        return view('contents.inventaris.barang_keluar', compact('barangKeluar', 'kendaraan', 'nopolKendaraan', 'merkKendaraan' ,'merkStokBarang', 'satuanStokBarang', 
        'typeStokBarang', 'ketStokBarang', 'sisaStokBarang', 'stok', 'namaStokBarang', 'satuan', 'namaSatuan'));
    }

    public function store(Request $request) {
        // Validasi input
        $request->validate([
            'jumlah.*' => 'required|numeric|min:1',
        ]);
        
        foreach ($request->tanggal_keluar as $index => $tanggalKeluar) {
            // Var
            $sisaStok = null;

            // Cek apakah jumlah tersedia dan validasi stok
            $jumlahKeluar = $request->jumlah[$index];
            if ($jumlahKeluar) {
                $dataStokBarang = StokBarang::find($request->id_stok_barang[$index]);

                if ($dataStokBarang) {
                    if ($dataStokBarang->jumlah >= $jumlahKeluar) {
                        $dataStokBarang->jumlah -= $jumlahKeluar;
                        $sisaStok = $dataStokBarang->jumlah;
                        $dataStokBarang->save();
                    } else {
                        // Jika jumlah yang keluar lebih dari stok yang tersedia
                        return redirect()->back()->withErrors('Jumlah keluar melebihi stok yang tersedia.');
                    }

                } else {
                    // Jika stok barang tidak ditemukan
                    return redirect()->back()->withErrors('Stok barang tidak ditemukan.');
                }
            }

            $dataBarangKeluar = [
                'id_stok_barang' => $request->id_stok_barang[$index],
                'id_kendaraan' => $request->kendaraan[$index],
                'tanggal_keluar' => $tanggalKeluar,
                'pengguna' => $request->pengguna[$index],
                'jumlah' => $request->jumlah[$index],
                'sisa_stok' => $sisaStok,
                'lokasi' => $request->lokasi[$index],
                'ket' => $request->ket[$index],
            ];

            BarangKeluar::create($dataBarangKeluar);
        }
        
        return redirect('barang_keluar');
    }

    public function update(Request $request) {
        $dataBarangKeluar = BarangKeluar::find($request->id_barang_keluar);
        if ($dataBarangKeluar) {
            $dataBarangKeluar->tanggal_keluar = $request->tanggal_keluar;
            $dataBarangKeluar->pengguna = $request->pengguna;
            $dataBarangKeluar->lokasi = $request->lokasi;
            $dataBarangKeluar->ket = $request->ket;
            $dataBarangKeluar->id_kendaraan = $request->kendaraan;
            $dataBarangKeluar->save();

            return redirect('barang_keluar')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('barang_keluar');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        foreach ($ids as $id) {
            $dataBarangKeluar = BarangKeluar::find($id);
            $dataStokBarang = StokBarang::find($dataBarangKeluar->id_stok_barang);

            if ($dataStokBarang) {
                $dataStokBarang->jumlah += $dataBarangKeluar->jumlah;
                $dataStokBarang->save();
            }
        }

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        BarangKeluar::whereIn('id_barang_keluar', $validatedIds)->delete();
        return redirect('barang_keluar');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;

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
            $barangKeluar = BarangKeluar::orderBy('tanggal_keluar', 'asc')->orderBy('id_stok_barang', 'asc')
            ->orderBy('id_kendaraan', 'asc')->get();
            return Excel::download(new BarangKeluarExport($mode, $barangKeluar, $rangeDate), 'Report Barang Keluar.xlsx');

        } else {
            $barangKeluar = BarangKeluar::where('tanggal_keluar', '>=', $start_date)
                ->where('tanggal_keluar', '<=', $end_date)
                ->orderBy('tanggal_keluar', 'asc')
                ->orderBy('id_stok_barang', 'asc')
                ->orderBy('id_kendaraan', 'asc')
                ->get();

            $fileName = 'Report Barang Keluar ' . $rangeDate . '.xlsx';
            return Excel::download(new BarangKeluarExport($mode, $barangKeluar, $rangeDate), $fileName);
        }
    }
}
