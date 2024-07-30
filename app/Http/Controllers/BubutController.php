<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanAMB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanExport;

class BubutController extends Controller
{
    public function index() {
        $bubut = TagihanAMB::where('keterangan', 'tagihan bubut')->orderBy('lokasi')->orderBy('tgl_order', 'asc')->get();
        $periodes = TagihanAMB::where('keterangan', 'tagihan bubut')
            ->select(TagihanAMB::raw('DATE_FORMAT(tgl_order, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        return view('contents.bubut', compact('bubut', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataBubut = [
            'keterangan' => 'tagihan bubut',
            'lokasi' => $request->lokasi,
            'pemesan' => $request->pemesan,
            'tgl_order' => $request->tgl_order,
            'tgl_invoice' => $request->tgl_invoice,
            'no_inventaris' => $request->no_inventaris,
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'dipakai_untuk' => $request->dipakai_untuk,
            'masa_pakai' => $request->masa_pakai,
            'jml' => $request->jml,
            'unit' => $request->unit,
            'harga' => $numericHarga,
            'total' => $numericTotal,
            'toko' => $request->toko
        ];

        $exitingBubut = TagihanAMB::where('keterangan', 'tagihan bubut')->where('lokasi', $request->lokasi)->where('pemesan', $request->pemesan)->where('tgl_order', $request->tgl_order)
            ->where('tgl_invoice', $request->tgl_invoice)->where('no_inventaris', $request->no_inventaris)->where('nama', $request->nama)->where('kategori', $request->kategori)
            ->where('dipakai_untuk', $request->dipakai_untuk)->where('masa_pakai', $request->masa_pakai)->where('jml', $request->jml)->where('unit', $request->unit)
            ->where('harga', $numericHarga)->where('total', $numericTotal)->where('toko', $request->toko)
            ->first();

        if ($exitingBubut) {
            $logErrors = 'Keterangan: ' . 'Tagihan Bubut' . ' - ' . 'Lokasi: ' . $request->lokasi . ' - ' . 'Pemesan: ' . $request->pemesan . ' - ' . 'Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . ' - ' . 
            'Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 'Kategori: ' . $request->kategori . ' - ' . 'Dipakai untuk: ' . $request->dipakai_untuk . ' - ' . 
            'Harga : ' . $request->harga . ' - ' . 'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';

            return redirect('bubut')->with('logErrors', $logErrors);

        } else {
            TagihanAMB::create($dataBubut);
            return redirect('bubut');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanBubut = TagihanAMB::find($request->id_tagihan_amb);
        if ($tagihanBubut) {
            $tagihanBubut->lokasi = $request->lokasi;
            $tagihanBubut->pemesan = $request->pemesan;
            $tagihanBubut->tgl_order = $request->tgl_order;
            $tagihanBubut->tgl_invoice = $request->tgl_invoice;
            $tagihanBubut->no_inventaris = $request->no_inventaris;
            $tagihanBubut->nama = $request->nama;
            $tagihanBubut->kategori = $request->kategori;
            $tagihanBubut->dipakai_untuk = $request->dipakai_untuk;
            $tagihanBubut->masa_pakai = $request->masa_pakai;
            $tagihanBubut->jml = $request->jml;
            $tagihanBubut->unit = $request->unit;
            $tagihanBubut->harga = $numericHarga;
            $tagihanBubut->total = $numericTotal;
            $tagihanBubut->toko = $request->toko;

            $tagihanBubut->save();
            return redirect('bubut')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('bubut');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->delete();
        return redirect('bubut');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $metode_pembelian = 'offline';

        $periode = $request->periode;
        $infoTagihan = 'Bubut';
    
        $hargaColumn = $metode_pembelian == 'online' ? 'harga_online' : null;
        $query = TagihanAMB::where('keterangan', 'tagihan bubut')
        ->when($hargaColumn, function ($query, $hargaColumn) {
            return $query->where($hargaColumn, '!=', null);
        }, function ($query) {
            return $query->whereNull('harga_online');
        });
        
        if ($mode != 'all_data') {
            $year = substr($periode, 0, 4);
            $month = substr($periode, 5, 2);
            $query->whereYear('tgl_order', '=', $year)
                  ->whereMonth('tgl_order', '=', $month);
        }
        
        $tagihan = $query->orderBy('tgl_order', 'asc')->orderBy('lokasi', 'asc')->orderBy('nama', 'asc')->get();
    
        // Tentukan nama file
        $fileName = $mode == 'all_data' 
            ? ($metode_pembelian == 'online' ? 'Report Bubut Online.xlsx' : 'Report Bubut.xlsx') 
            : ($metode_pembelian == 'online' 
                ? 'Report Bubut Online ' . \Carbon\Carbon::parse($periode)->format('M-Y') . '.xlsx' 
                : 'Report Bubut ' . \Carbon\Carbon::parse($periode)->format('M-Y') . '.xlsx');
    
        return Excel::download(new TagihanExport($mode, $tagihan, $infoTagihan, $metode_pembelian), $fileName);
    }
}
