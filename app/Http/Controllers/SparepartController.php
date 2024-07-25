<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanAMB;

class SparepartController extends Controller
{
    public function index() {
        $sparepartamb = TagihanAMB::whereIn('keterangan', ['tagihan sparepart', 'tagihan sparepart online'])->orderBy('lokasi')->get();
        $sparepartambgroup = TagihanAMB::whereIn('keterangan', ['tagihan sparepart', 'tagihan sparepart online'])
            ->selectRaw('lokasi, YEAR(tgl_order) as year, MONTHNAME(tgl_order) as month_name, SUM(total) as total_sum')
            ->groupBy('lokasi', 'year', 'month_name')
            ->orderBy('lokasi')
            ->orderBy('year')
            ->orderByRaw('MONTH(tgl_order)')
            ->get();

        return view('contents.sparepart_amb', compact('sparepartamb', 'sparepartambgroup'));
    }

    public function store(Request $request) {
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        if ($request->metode_pembelian == 'offline') {
            $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);

            $dataSparepartAMB = [
                'keterangan' => 'tagihan sparepart',
                'lokasi' => $request->lokasi,
                'nopol' => $request->nopol,
                'kode_unit' => $request->kode_unit,
                'merk' => $request->merk,
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
    
            $exitingSparepart = TagihanAMB::where('keterangan', 'tagihan sparepart')->where('lokasi', $request->lokasi)->where('nopol', $request->nopol)
                ->where('kode_unit', $request->kode_unit)->where('merk', $request->merk)->where('pemesan', $request->pemesan)->where('tgl_order', $request->tgl_order)
                ->where('tgl_invoice', $request->tgl_invoice)->where('no_inventaris', $request->no_inventaris)->where('nama', $request->nama)->where('kategori', $request->kategori)
                ->where('dipakai_untuk', $request->dipakai_untuk)->where('masa_pakai', $request->masa_pakai)->where('jml', $request->jml)->where('unit', $request->unit)
                ->where('harga', $numericHarga)->where('total', $numericTotal)->where('toko', $request->toko)
                ->first();
    
            if ($exitingSparepart) {
                $logErrors = 'Keterangan: ' . 'Tagihan Sparepart (Offline)' . ' - ' . 'Lokasi: ' . $request->lokasi . ' - ' . 'Pemesan: ' . $request->pemesan . ' - ' . 'Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . ' - ' . 
                'Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 'Kategori: ' . $request->kategori . ' - ' . 'Dipakai untuk: ' . $request->dipakai_untuk . ' - ' . 
                'Harga : ' . $request->harga . ' - ' . 'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';
    
                return redirect('sparepartamb')->with('logErrors', $logErrors);
    
            } else {
                TagihanAMB::create($dataSparepartAMB);
                return redirect('sparepartamb');
            }

        }

        if ($request->metode_pembelian == 'online') {
            $numericHargaOnline = preg_replace("/[^0-9]/", "", explode(",", $request->harga_online)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericDiskonOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->diskon_ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_jasa_aplikasi)[0]);

            $dataSparepartAMB = [
                'keterangan' => 'tagihan sparepart online',
                'lokasi' => $request->lokasi,
                'nopol' => $request->nopol,
                'kode_unit' => $request->kode_unit,
                'merk' => $request->merk,
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
    
            $exitingSparepart = TagihanAMB::where('keterangan', 'tagihan sparepart online')->where('lokasi', $request->lokasi)->where('nopol', $request->nopol)
                ->where('kode_unit', $request->kode_unit)->where('merk', $request->merk)->where('pemesan', $request->pemesan)->where('tgl_order', $request->tgl_order)
                ->where('tgl_invoice', $request->tgl_invoice)->where('no_inventaris', $request->no_inventaris)->where('nama', $request->nama)->where('kategori', $request->kategori)
                ->where('dipakai_untuk', $request->dipakai_untuk)->where('masa_pakai', $request->masa_pakai)->where('jml', $request->jml_onl)->where('unit', $request->unit_onl)
                ->where('harga_online', $numericHargaOnline)->where('ongkir', $numericOngkir)->where('diskon_ongkir', $numericDiskonOngkir)
                ->where('asuransi', $numericAsuransi)->where('b_proteksi', $numericProteksi)->where('b_jasa_aplikasi', $numericAplikasi)
                ->where('total', $numericTotal)->where('toko', $request->toko)
                ->first();
    
            if ($exitingSparepart) {
                $logErrors = 'Keterangan: ' . 'Tagihan Sparepart (Online)' . ' - ' . 'Lokasi: ' . $request->lokasi . ' - ' . 'Pemesan: ' . $request->pemesan . ' - ' . 'Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . ' - ' . 
                'Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 'Kategori: ' . $request->kategori . ' - ' . 'Dipakai untuk: ' . $request->dipakai_untuk . ' - ' . 
                'Harga : ' . $request->harga_online . ' - ' . 'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';
    
                return redirect('sparepartamb')->with('logErrors', $logErrors);
    
            } else {
                TagihanAMB::create($dataSparepartAMB);
                return redirect('sparepartamb');
            }

        }

        $dataSparepartAMB = [
            'keterangan' => 'tagihan sparepart',
            'lokasi' => $request->lokasi,
            'nopol' => $request->nopol,
            'kode_unit' => $request->kode_unit,
            'merk' => $request->merk,
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

        $exitingSparepart = TagihanAMB::where('keterangan', 'tagihan sparepart')->where('lokasi', $request->lokasi)->where('nopol', $request->nopol)
            ->where('kode_unit', $request->kode_unit)->where('merk', $request->merk)->where('pemesan', $request->pemesan)->where('tgl_order', $request->tgl_order)
            ->where('tgl_invoice', $request->tgl_invoice)->where('no_inventaris', $request->no_inventaris)->where('nama', $request->nama)->where('kategori', $request->kategori)
            ->where('dipakai_untuk', $request->dipakai_untuk)->where('masa_pakai', $request->masa_pakai)->where('total', $numericTotal)->where('toko', $request->toko)
            ->first();

        if ($exitingSparepart) {
            $logErrors = 'Keterangan: ' . 'Tagihan Sparepart' . ' - ' . 'Lokasi: ' . $request->lokasi . ' - ' . 'Pemesan: ' . $request->pemesan . ' - ' . 'Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . ' - ' . 
            'Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 'Kategori: ' . $request->kategori . ' - ' . 'Dipakai untuk: ' . $request->dipakai_untuk . ' - ' . 
            'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';

            return redirect('sparepartamb')->with('logErrors', $logErrors);

        } else {
            TagihanAMB::create($dataSparepartAMB);
            return redirect('sparepartamb');
        }
    }

    public function update(Request $request) {
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $tagihanSparepart = TagihanAMB::find($request->id_tagihan_amb);

        if ($request->metode_pembelian == 'offline') {
            $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);

            if ($tagihanSparepart) {
                $tagihanSparepart->keterangan = 'tagihan sparepart';
                $tagihanSparepart->lokasi = $request->lokasi;
                $tagihanSparepart->nopol = $request->nopol;
                $tagihanSparepart->kode_unit = $request->kode_unit;
                $tagihanSparepart->merk = $request->merk;
                $tagihanSparepart->pemesan = $request->pemesan;
                $tagihanSparepart->tgl_order = $request->tgl_order;
                $tagihanSparepart->tgl_invoice = $request->tgl_invoice;
                $tagihanSparepart->no_inventaris = $request->no_inventaris;
                $tagihanSparepart->nama = $request->nama;
                $tagihanSparepart->kategori = $request->kategori;
                $tagihanSparepart->dipakai_untuk = $request->dipakai_untuk;
                $tagihanSparepart->masa_pakai = $request->masa_pakai;
                $tagihanSparepart->jml = $request->jml;
                $tagihanSparepart->unit = $request->unit;
                $tagihanSparepart->harga = $numericHarga;
                $tagihanSparepart->harga_online = null;
                $tagihanSparepart->diskon_ongkir = null;
                $tagihanSparepart->ongkir = null;
                $tagihanSparepart->asuransi = null;
                $tagihanSparepart->b_proteksi = null;
                $tagihanSparepart->b_jasa_aplikasi = null;
                $tagihanSparepart->total = $numericTotal;
                $tagihanSparepart->toko = $request->toko;
    
                $tagihanSparepart->save();
                return redirect('sparepartamb')->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($request->metode_pembelian == 'online') {
            $numericHargaOnline = preg_replace("/[^0-9]/", "", explode(",", $request->harga_online)[0]);
            $numericDiskonOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->diskon_ongkir)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_jasa_aplikasi)[0]);

            if ($tagihanSparepart) {
                $tagihanSparepart->keterangan = 'tagihan sparepart online';
                $tagihanSparepart->lokasi = $request->lokasi;
                $tagihanSparepart->nopol = $request->nopol;
                $tagihanSparepart->kode_unit = $request->kode_unit;
                $tagihanSparepart->merk = $request->merk;
                $tagihanSparepart->pemesan = $request->pemesan;
                $tagihanSparepart->tgl_order = $request->tgl_order;
                $tagihanSparepart->tgl_invoice = $request->tgl_invoice;
                $tagihanSparepart->no_inventaris = $request->no_inventaris;
                $tagihanSparepart->nama = $request->nama;
                $tagihanSparepart->kategori = $request->kategori;
                $tagihanSparepart->dipakai_untuk = $request->dipakai_untuk;
                $tagihanSparepart->masa_pakai = $request->masa_pakai;
                $tagihanSparepart->jml = $request->jml_onl;
                $tagihanSparepart->unit = $request->unit_onl;
                $tagihanSparepart->harga = null;
                $tagihanSparepart->harga_online = $numericHargaOnline;
                $tagihanSparepart->diskon_ongkir = $numericDiskonOngkir;
                $tagihanSparepart->ongkir = $numericOngkir;
                $tagihanSparepart->asuransi = $numericAsuransi;
                $tagihanSparepart->b_proteksi = $numericProteksi;
                $tagihanSparepart->b_jasa_aplikasi = $numericAplikasi;
                $tagihanSparepart->total = $numericTotal;
                $tagihanSparepart->toko = $request->toko;
    
                $tagihanSparepart->save();
                return redirect('sparepartamb')->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($tagihanSparepart) {
            $tagihanSparepart->keterangan = 'tagihan sparepart';
            $tagihanSparepart->lokasi = $request->lokasi;
            $tagihanSparepart->nopol = $request->nopol;
            $tagihanSparepart->kode_unit = $request->kode_unit;
            $tagihanSparepart->merk = $request->merk;
            $tagihanSparepart->pemesan = $request->pemesan;
            $tagihanSparepart->tgl_order = $request->tgl_order;
            $tagihanSparepart->tgl_invoice = $request->tgl_invoice;
            $tagihanSparepart->no_inventaris = $request->no_inventaris;
            $tagihanSparepart->nama = $request->nama;
            $tagihanSparepart->kategori = $request->kategori;
            $tagihanSparepart->dipakai_untuk = $request->dipakai_untuk;
            $tagihanSparepart->masa_pakai = $request->masa_pakai;
            $tagihanSparepart->jml = null;
            $tagihanSparepart->unit = null;
            $tagihanSparepart->harga = null;
            $tagihanSparepart->harga_online = null;
            $tagihanSparepart->diskon_ongkir = null;
            $tagihanSparepart->ongkir = null;
            $tagihanSparepart->asuransi = null;
            $tagihanSparepart->b_proteksi = null;
            $tagihanSparepart->b_jasa_aplikasi = null;
            $tagihanSparepart->total = $numericTotal;
            $tagihanSparepart->toko = $request->toko;

            $tagihanSparepart->save();
            return redirect('sparepartamb')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('sparepartamb');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->delete();
        return redirect('sparepartamb');
    }

}
