<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanAMB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanExport;

class CatController extends Controller
{
    public function index() {
        $cat = TagihanAMB::whereIn('keterangan', ['tagihan cat', 'tagihan cat online'])->orderBy('lokasi')->orderBy('tgl_order')->orderBy('nama')->get();
        $catOnline = TagihanAMB::where('keterangan', 'tagihan cat online')->get();
        $catOffline = TagihanAMB::where('keterangan', 'tagihan cat')->get();
        $periodes = TagihanAMB::whereIn('keterangan', ['tagihan cat', 'tagihan cat online'])
            ->select(TagihanAMB::raw('DATE_FORMAT(tgl_order, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        return view('contents.cat_amb', compact('cat', 'catOnline', 'catOffline', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        if ($request->metode_pembelian == 'offline') {
            $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);

            $dataCatAMB = [
                'keterangan' => 'tagihan cat',
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
                'harga_online' => null,
                'ongkir' => null,
                'diskon_ongkir' => null,
                'asuransi' => null,
                'b_proteksi' => null,
                'b_jasa_aplikasi' => null,
                'total' => $numericTotal,
                'toko' => $request->toko
            ];
    
            $exitingCat = TagihanAMB::where('keterangan', 'tagihan cat')->where('lokasi', $request->lokasi)->where('pemesan', $request->pemesan)
                ->where('tgl_order', $request->tgl_order)->where('tgl_invoice', $request->tgl_invoice)->where('no_inventaris', $request->no_inventaris)
                ->where('nama', $request->nama)->where('kategori', $request->kategori)->where('dipakai_untuk', $request->dipakai_untuk)
                ->where('masa_pakai', $request->masa_pakai)->where('jml', $request->jml)->where('unit', $request->unit)
                ->where('harga', $numericHarga)->where('total', $numericTotal)->where('toko', $request->toko)
                ->first();
    
            if ($exitingCat) {
                $logErrors = 'Keterangan: ' . 'Tagihan Cat (Offline)' . ' - ' . 'Lokasi: ' . $request->lokasi . ' - ' . 'Pemesan: ' . $request->pemesan . ' - ' . 'Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . ' - ' . 
                'Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 'Kategori: ' . $request->kategori . ' - ' . 'Dipakai untuk: ' . $request->dipakai_untuk . ' - ' . 
                'Harga : ' . $request->harga . ' - ' . 'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';
    
                return redirect('cat')->with('logErrors', $logErrors);
    
            } else {
                TagihanAMB::create($dataCatAMB);
                return redirect('cat');
            }

        }

        if ($request->metode_pembelian == 'online') {
            $numericHargaOnline = preg_replace("/[^0-9]/", "", explode(",", $request->harga_online)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericDiskonOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->diskon_ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_jasa_aplikasi)[0]);

            $dataCatAMB = [
                'keterangan' => 'tagihan cat online',
                'lokasi' => $request->lokasi,
                'pemesan' => $request->pemesan,
                'tgl_order' => $request->tgl_order,
                'tgl_invoice' => $request->tgl_invoice,
                'no_inventaris' => $request->no_inventaris,
                'nama' => $request->nama,
                'kategori' => $request->kategori,
                'dipakai_untuk' => $request->dipakai_untuk,
                'masa_pakai' => $request->masa_pakai,
                'jml' => $request->jml_onl,
                'unit' => $request->unit_onl,
                'harga' => null,
                'harga_online' => $numericHargaOnline,
                'ongkir' => $numericOngkir,
                'diskon_ongkir' => $numericDiskonOngkir,
                'asuransi' => $numericAsuransi,
                'b_proteksi' => $numericProteksi,
                'b_jasa_aplikasi' => $numericAplikasi,
                'total' => $numericTotal,
                'toko' => $request->toko
            ];
    
            $exitingCat = TagihanAMB::where('keterangan', 'tagihan cat online')->where('lokasi', $request->lokasi)
                ->where('pemesan', $request->pemesan)->where('tgl_order', $request->tgl_order)
                ->where('tgl_invoice', $request->tgl_invoice)->where('no_inventaris', $request->no_inventaris)->where('nama', $request->nama)->where('kategori', $request->kategori)
                ->where('dipakai_untuk', $request->dipakai_untuk)->where('masa_pakai', $request->masa_pakai)->where('jml', $request->jml_onl)->where('unit', $request->unit_onl)
                ->where('harga_online', $numericHargaOnline)->where('ongkir', $numericOngkir)->where('diskon_ongkir', $numericDiskonOngkir)
                ->where('asuransi', $numericAsuransi)->where('b_proteksi', $numericProteksi)->where('b_jasa_aplikasi', $numericAplikasi)
                ->where('total', $numericTotal)->where('toko', $request->toko)
                ->first();
    
            if ($exitingCat) {
                $logErrors = 'Keterangan: ' . 'Tagihan Cat (Online)' . ' - ' . 'Lokasi: ' . $request->lokasi . ' - ' . 'Pemesan: ' . $request->pemesan . ' - ' . 'Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . ' - ' . 
                'Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 'Kategori: ' . $request->kategori . ' - ' . 'Dipakai untuk: ' . $request->dipakai_untuk . ' - ' . 
                'Harga : ' . $request->harga_online . ' - ' . 'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';
    
                return redirect('cat')->with('logErrors', $logErrors);
    
            } else {
                TagihanAMB::create($dataCatAMB);
                return redirect('cat');
            }

        }

        $dataCatAMB = [
            'keterangan' => 'tagihan cat',
            'lokasi' => $request->lokasi,
            'pemesan' => $request->pemesan,
            'tgl_order' => $request->tgl_order,
            'tgl_invoice' => $request->tgl_invoice,
            'no_inventaris' => $request->no_inventaris,
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'dipakai_untuk' => $request->dipakai_untuk,
            'masa_pakai' => $request->masa_pakai,
            'jml' => null,
            'unit' => null,
            'harga' => null,
            'harga_online' => null,
            'ongkir' => null,
            'diskon_ongkir' => null,
            'asuransi' => null,
            'b_proteksi' => null,
            'b_jasa_aplikasi' => null,
            'total' => $numericTotal,
            'toko' => $request->toko
        ];

        $exitingCat = TagihanAMB::where('keterangan', 'tagihan cat')->where('lokasi', $request->lokasi)
            ->where('pemesan', $request->pemesan)->where('tgl_order', $request->tgl_order)
            ->where('tgl_invoice', $request->tgl_invoice)->where('no_inventaris', $request->no_inventaris)->where('nama', $request->nama)->where('kategori', $request->kategori)
            ->where('dipakai_untuk', $request->dipakai_untuk)->where('masa_pakai', $request->masa_pakai)->where('total', $numericTotal)->where('toko', $request->toko)
            ->first();

        if ($exitingCat) {
            $logErrors = 'Keterangan: ' . 'Tagihan Cat (Offline)' . ' - ' . 'Lokasi: ' . $request->lokasi . ' - ' . 'Pemesan: ' . $request->pemesan . ' - ' . 'Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . ' - ' . 
            'Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 'Kategori: ' . $request->kategori . ' - ' . 'Dipakai untuk: ' . $request->dipakai_untuk . ' - ' . 
            'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';

            return redirect('cat')->with('logErrors', $logErrors);

        } else {
            TagihanAMB::create($dataCatAMB);
            return redirect('cat');
        }
    }

    public function update(Request $request) {
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $tagihanCat = TagihanAMB::find($request->id_tagihan_amb);

        if ($request->metode_pembelian == 'offline') {
            $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);

            if ($tagihanCat) {
                $tagihanCat->keterangan = 'tagihan cat';
                $tagihanCat->lokasi = $request->lokasi;
                $tagihanCat->pemesan = $request->pemesan;
                $tagihanCat->tgl_order = $request->tgl_order;
                $tagihanCat->tgl_invoice = $request->tgl_invoice;
                $tagihanCat->no_inventaris = $request->no_inventaris;
                $tagihanCat->nama = $request->nama;
                $tagihanCat->kategori = $request->kategori;
                $tagihanCat->dipakai_untuk = $request->dipakai_untuk;
                $tagihanCat->masa_pakai = $request->masa_pakai;
                $tagihanCat->jml = $request->jml;
                $tagihanCat->unit = $request->unit;
                $tagihanCat->harga = $numericHarga;
                $tagihanCat->harga_online = null;
                $tagihanCat->diskon_ongkir = null;
                $tagihanCat->ongkir = null;
                $tagihanCat->asuransi = null;
                $tagihanCat->b_proteksi = null;
                $tagihanCat->b_jasa_aplikasi = null;
                $tagihanCat->total = $numericTotal;
                $tagihanCat->toko = $request->toko;
    
                $tagihanCat->save();
                return redirect('cat')->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($request->metode_pembelian == 'online') {
            $numericHargaOnline = preg_replace("/[^0-9]/", "", explode(",", $request->harga_online)[0]);
            $numericDiskonOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->diskon_ongkir)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_jasa_aplikasi)[0]);

            if ($tagihanCat) {
                $tagihanCat->keterangan = 'tagihan cat online';
                $tagihanCat->lokasi = $request->lokasi;
                $tagihanCat->nopol = $request->nopol;
                $tagihanCat->kode_unit = $request->kode_unit;
                $tagihanCat->merk = $request->merk;
                $tagihanCat->pemesan = $request->pemesan;
                $tagihanCat->tgl_order = $request->tgl_order;
                $tagihanCat->tgl_invoice = $request->tgl_invoice;
                $tagihanCat->no_inventaris = $request->no_inventaris;
                $tagihanCat->nama = $request->nama;
                $tagihanCat->kategori = $request->kategori;
                $tagihanCat->dipakai_untuk = $request->dipakai_untuk;
                $tagihanCat->masa_pakai = $request->masa_pakai;
                $tagihanCat->jml = $request->jml_onl;
                $tagihanCat->unit = $request->unit_onl;
                $tagihanCat->harga = null;
                $tagihanCat->harga_online = $numericHargaOnline;
                $tagihanCat->diskon_ongkir = $numericDiskonOngkir;
                $tagihanCat->ongkir = $numericOngkir;
                $tagihanCat->asuransi = $numericAsuransi;
                $tagihanCat->b_proteksi = $numericProteksi;
                $tagihanCat->b_jasa_aplikasi = $numericAplikasi;
                $tagihanCat->total = $numericTotal;
                $tagihanCat->toko = $request->toko;
    
                $tagihanCat->save();
                return redirect('cat')->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($tagihanCat) {
            $tagihanCat->keterangan = 'tagihan cat';
            $tagihanCat->lokasi = $request->lokasi;
            $tagihanCat->nopol = $request->nopol;
            $tagihanCat->kode_unit = $request->kode_unit;
            $tagihanCat->merk = $request->merk;
            $tagihanCat->pemesan = $request->pemesan;
            $tagihanCat->tgl_order = $request->tgl_order;
            $tagihanCat->tgl_invoice = $request->tgl_invoice;
            $tagihanCat->no_inventaris = $request->no_inventaris;
            $tagihanCat->nama = $request->nama;
            $tagihanCat->kategori = $request->kategori;
            $tagihanCat->dipakai_untuk = $request->dipakai_untuk;
            $tagihanCat->masa_pakai = $request->masa_pakai;
            $tagihanCat->jml = null;
            $tagihanCat->unit = null;
            $tagihanCat->harga = null;
            $tagihanCat->harga_online = null;
            $tagihanCat->diskon_ongkir = null;
            $tagihanCat->ongkir = null;
            $tagihanCat->asuransi = null;
            $tagihanCat->b_proteksi = null;
            $tagihanCat->b_jasa_aplikasi = null;
            $tagihanCat->total = $numericTotal;
            $tagihanCat->toko = $request->toko;

            $tagihanCat->save();
            return redirect('cat')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('cat');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->delete();
        return redirect('cat');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $metode_pembelian = 'offline';
        
        if ($request->metode_pembelian) {
            $metode_pembelian = $request->metode_pembelian;
        }

        $periode = $request->periode;
        $infoTagihan = 'Cat';
    
        $hargaColumn = $metode_pembelian == 'online' ? 'harga_online' : null;
        $query = TagihanAMB::whereIn('keterangan', ['tagihan cat', 'tagihan cat online'])
        ->when($hargaColumn, function ($query, $hargaColumn) {
            return $query->where($hargaColumn, '!=', null)
                        ->where('keterangan', 'tagihan cat online');
        }, function ($query) {
            return $query->whereNull('harga_online')
                        ->where('keterangan', 'tagihan cat');
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
            ? ($metode_pembelian == 'online' ? 'Report Cat Online.xlsx' : 'Report Cat.xlsx') 
            : ($metode_pembelian == 'online' 
                ? 'Report Cat Online ' . \Carbon\Carbon::parse($periode)->format('M-Y') . '.xlsx' 
                : 'Report Cat ' . \Carbon\Carbon::parse($periode)->format('M-Y') . '.xlsx');
    
        return Excel::download(new TagihanExport($mode, $tagihan, $infoTagihan, $metode_pembelian), $fileName);
    }
}
