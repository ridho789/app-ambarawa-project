<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanAMB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanExport;

class ACController extends Controller
{
    public function index() {
        $ac = TagihanAMB::where('keterangan', 'tagihan ac')->orderBy('lokasi')->orderBy('tgl_order', 'asc')->get();
        $periodes = TagihanAMB::where('keterangan', 'tagihan ac')
            ->select(TagihanAMB::raw('DATE_FORMAT(tgl_order, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        return view('contents.ac', compact('ac', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataSparepartAMB = [
            'keterangan' => 'tagihan ac',
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

        $exitingSparepart = TagihanAMB::where('keterangan', 'tagihan ac')->where('lokasi', $request->lokasi)->where('pemesan', $request->pemesan)->where('tgl_order', $request->tgl_order)
            ->where('tgl_invoice', $request->tgl_invoice)->where('no_inventaris', $request->no_inventaris)->where('nama', $request->nama)->where('kategori', $request->kategori)
            ->where('dipakai_untuk', $request->dipakai_untuk)->where('masa_pakai', $request->masa_pakai)->where('jml', $request->jml)->where('unit', $request->unit)
            ->where('harga', $numericHarga)->where('total', $numericTotal)->where('toko', $request->toko)
            ->first();

        if ($exitingSparepart) {
            $logErrors = 'Keterangan: ' . 'Tagihan AC' . ' - ' . 'Lokasi: ' . $request->lokasi . ' - ' . 'Pemesan: ' . $request->pemesan . ' - ' . 'Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . ' - ' . 
            'Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 'Kategori: ' . $request->kategori . ' - ' . 'Dipakai untuk: ' . $request->dipakai_untuk . ' - ' . 
            'Harga : ' . $request->harga . ' - ' . 'Toko: ' . $request->toko . ', data yang di input sudah ada di sistem';

            return redirect('ac')->with('logErrors', $logErrors);

        } else {
            TagihanAMB::create($dataSparepartAMB);
            return redirect('ac');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanAC = TagihanAMB::find($request->id_tagihan_amb);
        if ($tagihanAC) {
            $tagihanAC->lokasi = $request->lokasi;
            $tagihanAC->pemesan = $request->pemesan;
            $tagihanAC->tgl_order = $request->tgl_order;
            $tagihanAC->tgl_invoice = $request->tgl_invoice;
            $tagihanAC->no_inventaris = $request->no_inventaris;
            $tagihanAC->nama = $request->nama;
            $tagihanAC->kategori = $request->kategori;
            $tagihanAC->dipakai_untuk = $request->dipakai_untuk;
            $tagihanAC->masa_pakai = $request->masa_pakai;
            $tagihanAC->jml = $request->jml;
            $tagihanAC->unit = $request->unit;
            $tagihanAC->harga = $numericHarga;
            $tagihanAC->total = $numericTotal;
            $tagihanAC->toko = $request->toko;

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
        $periode = $request->periode;
        $infoTagihan = 'AC';

        if ($mode == 'all_data') {
            $tagihan = TagihanAMB::where('keterangan', 'tagihan ac')->orderBy('tgl_order', 'asc')->get();
            return Excel::download(new TagihanExport($mode, $tagihan, $infoTagihan), 'Report AC.xlsx');

        } else {
            $tagihan = TagihanAMB::where('keterangan', 'tagihan ac')->whereYear('tgl_order', '=', substr($periode, 0, 4))
                ->whereMonth('tgl_order', '=', substr($periode, 5, 2))
                ->orderBy('lokasi')
                ->orderBy('tgl_order', 'asc')
                ->get();

            $fileName = 'Report AC ' . \Carbon\Carbon::parse($periode)->format('M-Y') . '.xlsx';
            return Excel::download(new TagihanExport($mode, $tagihan, $infoTagihan), $fileName);
        }
    }
}
