<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanAMB;
use App\Models\Satuan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanExport;
use Carbon\Carbon;

class PolesKacaMobilController extends Controller
{
    public function index() {
        $poles = TagihanAMB::where('keterangan', 'tagihan poles kaca mobil')->orderBy('lokasi')->get();
        $periodes = TagihanAMB::where('keterangan', 'tagihan poles kaca mobil')
            ->select(TagihanAMB::raw('DATE_FORMAT(tgl_order, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        $satuan = Satuan::all();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        return view('contents.poles_kaca_mobil', compact('poles', 'periodes', 'satuan', 'namaSatuan'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $masa_pakai = $request->masa . ' ' . $request->waktu;

        $dataPoles = [
            'keterangan' => 'tagihan poles kaca mobil',
            'lokasi' => $request->lokasi,
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
            'toko' => $request->toko
        ];

        $exitingPoles = TagihanAMB::where('keterangan', 'tagihan poles kaca mobil')->where('lokasi', $request->lokasi)->where('pemesan', $request->pemesan)
            ->where('tgl_order', $request->tgl_order)->where('tgl_invoice', $request->tgl_invoice)->where('no_inventaris', $request->no_inventaris)
            ->where('nama', $request->nama)->where('kategori', $request->kategori)->where('dipakai_untuk', $request->dipakai_untuk)->where('masa_pakai', $request->masa_pakai)
            ->where('jml', $request->jml)->where('id_satuan', $request->unit)->where('harga', $numericHarga)->where('total', $numericTotal)->where('toko', $request->toko)
            ->first();

        if ($exitingPoles) {
            $logErrors = 'Keterangan: ' . 'Tagihan Poles Kaca Mobil' . ' - ' . 'Lokasi: ' . $request->lokasi . ' - ' . 'Pemesan: ' . $request->pemesan . ' - ' . 'Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . ' - ' . 
            'Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 'Kategori: ' . $request->kategori . ' - ' . 'Dipakai untuk: ' . $request->dipakai_untuk . ' - ' . 
            'Harga : ' . $request->harga . ' - ' . 'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';

            return redirect('poles')->with('logErrors', $logErrors);

        } else {
            TagihanAMB::create($dataPoles);
            return redirect('poles');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $masa_pakai = $request->masa . ' ' . $request->waktu;
        
        $tagihanPoles = TagihanAMB::find($request->id_tagihan_amb);
        if ($tagihanPoles) {
            $tagihanPoles->lokasi = $request->lokasi;
            $tagihanPoles->pemesan = $request->pemesan;
            $tagihanPoles->tgl_order = $request->tgl_order;
            $tagihanPoles->tgl_invoice = $request->tgl_invoice;
            $tagihanPoles->no_inventaris = $request->no_inventaris;
            $tagihanPoles->nama = $request->nama;
            $tagihanPoles->kategori = $request->kategori;
            $tagihanPoles->dipakai_untuk = $request->dipakai_untuk;
            $tagihanPoles->masa_pakai = $masa_pakai;
            $tagihanPoles->jml = $request->jml;
            $tagihanPoles->id_satuan = $request->unit;
            $tagihanPoles->harga = $numericHarga;
            $tagihanPoles->total = $numericTotal;
            $tagihanPoles->toko = $request->toko;

            $tagihanPoles->save();
            return redirect('poles')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('poles');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->delete();
        return redirect('poles');
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

        $infoTagihan = 'Poles';
        $hargaColumn = $metode_pembelian == 'online' ? 'harga_online' : null;
        $query = TagihanAMB::where('keterangan', 'tagihan poles kaca mobil')
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
            ? ($metode_pembelian == 'online' ? 'Report Poles Online.xlsx' : 'Report Poles.xlsx') 
            : ($metode_pembelian == 'online' 
                ? 'Report Poles Online ' . $rangeDate . '.xlsx' 
                : 'Report Poles ' . $rangeDate . '.xlsx');
    
        return Excel::download(new TagihanExport($mode, $tagihan, $infoTagihan, $metode_pembelian, $rangeDate), $fileName);
    }
}
