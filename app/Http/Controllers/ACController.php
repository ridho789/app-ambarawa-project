<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanAMB;
use App\Models\Kendaraan;
use App\Models\Satuan;
use App\Models\Toko;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanNopolExport;
use Carbon\Carbon;
use DateTime;

class ACController extends Controller
{
    public function index() {
        $ac = TagihanAMB::where('keterangan', 'tagihan ac')->orderBy('lokasi')->orderBy('tgl_order', 'asc')->get();
        $kendaraan = Kendaraan::orderBy('nopol')->get();
        $nopolKendaraan = Kendaraan::pluck('nopol', 'id_kendaraan');
        $merkKendaraan = Kendaraan::pluck('merk', 'id_kendaraan');
        $satuan = Satuan::orderBy('nama')->get();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        $toko = Toko::orderBy('nama')->get();
        $namaToko = Toko::pluck('nama', 'id_toko');
        return view('contents.ac', compact('ac', 'kendaraan', 'nopolKendaraan', 'merkKendaraan', 'satuan', 'namaSatuan', 'toko', 'namaToko'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $masa_pakai = $request->masa . ' ' . $request->waktu;

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
            $filePath = $file->storeAs('AC', $fileName, 'public');
        }

        $dataAC = [
            'keterangan' => 'tagihan ac',
            'lokasi' => $request->lokasi,
            'id_kendaraan' => $request->kendaraan,
            'pemesan' => $request->pemesan,
            'tgl_order' => $request->tgl_order,
            'tgl_invoice' => $request->tgl_invoice,
            'no_inventaris' => $request->no_inventaris,
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'dipakai_untuk' => $request->dipakai_untuk,
            'masa_pakai' => $masa_pakai,
            'jml' => $request->jml,
            'id_satuan' => $request->unit,
            'harga' => $numericHarga,
            'total' => $numericTotal,
            'id_toko' => $request->toko,
            'file' => $filePath
        ];

        $dataToko = Toko::where('id_toko', $request->toko)->first();
        $namaToko = 'null';
        if ($dataToko) {
            $namaToko = $dataToko->nama;
        }

        $exitingAC = TagihanAMB::where('keterangan', 'tagihan ac')
            ->where('lokasi', $request->lokasi)
            ->where('id_kendaraan', $request->kendaraan)
            ->where('pemesan', $request->pemesan)
            ->where('tgl_order', $request->tgl_order)
            ->where('tgl_invoice', $request->tgl_invoice)
            ->where('no_inventaris', $request->no_inventaris)
            ->where('nama', $request->nama)
            ->where('kategori', $request->kategori)
            ->where('dipakai_untuk', $request->dipakai_untuk)
            ->where('masa_pakai', $masa_pakai)
            ->where('jml', $request->jml)
            ->where('id_satuan', $request->unit)
            ->where('harga', $numericHarga)
            ->where('total', $numericTotal)
            ->where('id_toko', $request->toko)
            ->first();

        if ($exitingAC) {
            $logErrors = 'Keterangan: Tagihan AC - Lokasi: ' . $request->lokasi . 
                ' - Pemesan: ' . $request->pemesan . 
                ' - Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . 
                ' - Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . 
                ' - Nama: ' . $request->nama . 
                ' - Kategori: ' . $request->kategori . 
                ' - Dipakai untuk: ' . $request->dipakai_untuk . 
                ' - Harga: ' . $request->harga . 
                ' - Toko: ' . $namaToko . ', data yang di input sudah ada di sistem';
        
            return redirect('ac')->with('logErrors', $logErrors);

        } else {
            TagihanAMB::create($dataAC);
            return redirect('ac');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $masa_pakai = $request->masa . ' ' . $request->waktu;

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
            $filePath = $file->storeAs('AC', $fileName, 'public');
        }
        
        $tagihanAC = TagihanAMB::find($request->id_tagihan_amb);
        if ($tagihanAC) {
            $tagihanAC->lokasi = $request->lokasi;
            $tagihanAC->id_kendaraan = $request->kendaraan;
            $tagihanAC->pemesan = $request->pemesan;
            $tagihanAC->tgl_order = $request->tgl_order;
            $tagihanAC->tgl_invoice = $request->tgl_invoice;
            $tagihanAC->no_inventaris = $request->no_inventaris;
            $tagihanAC->nama = $request->nama;
            $tagihanAC->kategori = $request->kategori;
            $tagihanAC->dipakai_untuk = $request->dipakai_untuk;
            $tagihanAC->masa_pakai = $masa_pakai;
            $tagihanAC->jml = $request->jml;
            $tagihanAC->id_satuan = $request->unit;
            $tagihanAC->harga = $numericHarga;
            $tagihanAC->total = $numericTotal;
            $tagihanAC->id_toko = $request->toko;

            if ($filePath) {
                $tagihanAC->file = $filePath;
            }

            $tagihanAC->save();
            return redirect('ac')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('ac');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->delete();
        return redirect('ac');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $metode_pembelian = 'offline';

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

        $infoTagihan = 'AC';
        $hargaColumn = $metode_pembelian == 'online' ? 'harga_online' : null;
        $query = TagihanAMB::where('keterangan', 'tagihan ac')
        ->when($hargaColumn, function ($query, $hargaColumn) {
            return $query->where($hargaColumn, '!=', null);
        }, function ($query) {
            return $query->whereNull('harga_online');
        });
        
        if ($mode != 'all_data') {
            $query->where('tgl_order', '>=', $start_date)
                  ->where('tgl_order', '<=', $end_date);
        }
        
        $tagihan = $query->orderBy('tgl_order', 'asc')->orderBy('lokasi', 'asc')->orderBy('nama', 'asc')->get();
    
        // Tentukan nama file
        $fileName = $mode == 'all_data' 
            ? ($metode_pembelian == 'online' ? 'Report AC Online.xlsx' : 'Report AC.xlsx') 
            : ($metode_pembelian == 'online' 
                ? 'Report AC Online ' . $rangeDate . '.xlsx' 
                : 'Report AC ' . $rangeDate . '.xlsx');
    
        return Excel::download(new TagihanNopolExport($mode, $tagihan, $infoTagihan, $metode_pembelian, $rangeDate), $fileName);
    }

    // Update status
    public function pending(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->update([
            'status' => 'pending'
        ]);
        return redirect('ac');
    }

    public function process(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->update([
            'status' => 'processing'
        ]);
        return redirect('ac');
    }

    public function paid(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->update([
            'status' => 'paid'
        ]);
        return redirect('ac');
    }
}
