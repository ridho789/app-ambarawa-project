<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operasional;

class OperasionalController extends Controller
{
    public function index() {
        $operasional = Operasional::orderBy('nama', 'asc')->get();
        return view('contents.operasional', compact('operasional'));
    }

    public function store(Request $request) {
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        if ($request->metode_pembelian == 'offline') {
            $numericHargaToko = preg_replace("/[^0-9]/", "", explode(",", $request->harga_toko)[0]);

            $dataOperasional = [
                'tanggal' => $request->tanggal,
                'uraian' => $request->uraian,
                'qty' => $request->qty,
                'unit' => $request->unit,
                'deskripsi' => $request->deskripsi,
                'nama' => $request->nama,
                'harga_toko' => $numericHargaToko,
                'total' => $numericTotal,
                'toko' => $request->toko
            ];

            $exitingOperasional = Operasional::where('tanggal', $request->tanggal)->where('uraian', $request->uraian)->where('qty', $request->qty)
                ->where('unit', $request->unit)->where('deskripsi', $request->deskripsi)->where('nama', $request->nama)
                ->where('harga_toko', $numericHargaToko)->where('total', $numericTotal)->where('toko', $request->toko)->first();
    
            if ($exitingOperasional) {
                $logErrors = 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Uraian: ' . $request->uraian . ' - ' . 
                'Jumlah: ' . $request->qty . ' - ' . 'Satuan: ' . $request->unit . ' - ' . 'Deskripsi: ' . $request->deskripsi . ' - ' . 'Nama: ' . $request->nama . 
                ' - ' . 'Total: ' . $request->total . ' - ' . 'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';
    
                return redirect('operasional')->with('logErrors', $logErrors);
    
            } else {
                Operasional::create($dataOperasional);
                return redirect('operasional');
            }

        }

        if ($request->metode_pembelian == 'online') {
            $numericDiskon = preg_replace("/[^0-9]/", "", explode(",", $request->diskon)[0]);
            $numericHargaOnl = preg_replace("/[^0-9]/", "", explode(",", $request->harga_onl)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericMember = preg_replace("/[^0-9]/", "", explode(",", $request->p_member)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_aplikasi)[0]);
            $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
    
            $dataOperasional = [
                'tanggal' => $request->tanggal,
                'uraian' => $request->uraian,
                'qty' => $request->qty_onl,
                'unit' => $request->unit_onl,
                'deskripsi' => $request->deskripsi,
                'nama' => $request->nama,
                'diskon' => $numericDiskon,
                'harga_onl' => $numericHargaOnl,
                'ongkir' => $numericOngkir,
                'asuransi' => $numericAsuransi,
                'b_proteksi' => $numericProteksi,
                'p_member' => $numericMember,
                'b_aplikasi' => $numericAplikasi,
                'total' => $numericTotal,
                'toko' => $request->toko
            ];
            
            $exitingOperasional = Operasional::where('tanggal', $request->tanggal)->where('uraian', $request->uraian)->where('qty', $request->qty)
                ->where('unit', $request->unit)->where('deskripsi', $request->deskripsi)->where('nama', $request->nama)
                ->where('diskon', $numericDiskon)->where('harga_onl', $numericHargaOnl)->where('ongkir', $numericOngkir)
                ->where('asuransi', $numericAsuransi)->where('b_proteksi', $numericProteksi)->where('p_member', $numericMember)->where('b_aplikasi', $numericAplikasi)
                ->where('total', $numericTotal)->where('toko', $request->toko)->first();
    
            if ($exitingOperasional) {
                $logErrors = 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Uraian: ' . $request->uraian . ' - ' . 
                'Jumlah: ' . $request->qty . ' - ' . 'Satuan: ' . $request->unit . ' - ' . 'Deskripsi: ' . $request->deskripsi . ' - ' . 'Nama: ' . $request->nama . 
                ' - ' . 'Total: ' . $request->total . ' - ' . 'Toko: ' . $request->toko . ', data tersebut sudah ada di sistem';
    
                return redirect('operasional')->with('logErrors', $logErrors);
    
            } else {
                Operasional::create($dataOperasional);
                return redirect('operasional');
            }
        }

        $dataOperasional = [
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'deskripsi' => $request->deskripsi,
            'nama' => $request->nama,
            'total' => $numericTotal,
            'toko' => $request->toko
        ];

        $exitingOperasional = Operasional::where('tanggal', $request->tanggal)->where('uraian', $request->uraian)->where('deskripsi', $request->deskripsi)
        ->where('nama', $request->nama)->where('total', $numericTotal)->where('toko', $request->toko)->first();

        if ($exitingOperasional) {
            $logErrors = 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Uraian: ' . $request->uraian . ' - ' . 
            'Deskripsi: ' . $request->deskripsi . ' - ' . 'Nama: ' . $request->nama . ' - ' . 'Total: ' . $request->total . ' - ' . 'Toko: ' . $request->toko . 
            ', data tersebut sudah ada di sistem';

            return redirect('operasional')->with('logErrors', $logErrors);

        } else {
            Operasional::create($dataOperasional);
            return redirect('operasional');
        }
    }

    public function update(Request $request) {
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $tagihanOperasional = Operasional::find($request->id_operasional);

        if ($request->metode_pembelian == 'offline') {
            $numericHargaToko = preg_replace("/[^0-9]/", "", explode(",", $request->harga_toko)[0]);

            if ($tagihanOperasional) {
                $tagihanOperasional->tanggal = $request->tanggal;
                $tagihanOperasional->uraian = $request->uraian;
                $tagihanOperasional->deskripsi = $request->deskripsi;
                $tagihanOperasional->nama = $request->nama;
                $tagihanOperasional->qty = $request->qty;
                $tagihanOperasional->unit = $request->unit;
                $tagihanOperasional->harga_toko = $numericHargaToko;
                $tagihanOperasional->harga_onl = null;
                $tagihanOperasional->diskon = null;
                $tagihanOperasional->ongkir = null;
                $tagihanOperasional->asuransi = null;
                $tagihanOperasional->b_proteksi = null;
                $tagihanOperasional->p_member = null;
                $tagihanOperasional->b_aplikasi = null;
                $tagihanOperasional->total = $numericTotal;
                $tagihanOperasional->toko = $request->toko;
    
                $tagihanOperasional->save();
                return redirect('operasional')->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($request->metode_pembelian == 'online') {
            $numericHargaOnl = preg_replace("/[^0-9]/", "", explode(",", $request->harga_onl)[0]);
            $numericDiskon = preg_replace("/[^0-9]/", "", explode(",", $request->diskon)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericMember = preg_replace("/[^0-9]/", "", explode(",", $request->p_member)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_aplikasi)[0]);

            if ($tagihanOperasional) {
                $tagihanOperasional->tanggal = $request->tanggal;
                $tagihanOperasional->uraian = $request->uraian;
                $tagihanOperasional->deskripsi = $request->deskripsi;
                $tagihanOperasional->nama = $request->nama;
                $tagihanOperasional->qty = $request->qty_onl;
                $tagihanOperasional->unit = $request->unit_onl;
                $tagihanOperasional->harga_toko = null;
                $tagihanOperasional->harga_onl = $numericHargaOnl;
                $tagihanOperasional->diskon = $numericDiskon;
                $tagihanOperasional->ongkir = $numericOngkir;
                $tagihanOperasional->asuransi = $numericAsuransi;
                $tagihanOperasional->b_proteksi = $numericProteksi;
                $tagihanOperasional->p_member = $numericMember;
                $tagihanOperasional->b_aplikasi = $numericAplikasi;
                $tagihanOperasional->total = $numericTotal;
                $tagihanOperasional->toko = $request->toko;
    
                $tagihanOperasional->save();
                return redirect('operasional')->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($tagihanOperasional) {
            $tagihanOperasional->tanggal = $request->tanggal;
            $tagihanOperasional->uraian = $request->uraian;
            $tagihanOperasional->deskripsi = $request->deskripsi;
            $tagihanOperasional->nama = $request->nama;
            $tagihanOperasional->qty = null;
            $tagihanOperasional->unit = null;
            $tagihanOperasional->harga_toko = null;
            $tagihanOperasional->harga_onl = null;
            $tagihanOperasional->diskon = null;
            $tagihanOperasional->ongkir = null;
            $tagihanOperasional->asuransi = null;
            $tagihanOperasional->b_proteksi = null;
            $tagihanOperasional->p_member = null;
            $tagihanOperasional->b_aplikasi = null;
            $tagihanOperasional->total = $numericTotal;
            $tagihanOperasional->toko = $request->toko;

            $tagihanOperasional->save();
            return redirect('operasional')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('operasional');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Operasional::whereIn('id_operasional', $validatedIds)->delete();
        return redirect('operasional');
    }
}
