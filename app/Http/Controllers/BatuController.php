<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembangunan;
use App\Models\Proyek;

class BatuController extends Controller
{
    public function index() {
        $batu = Pembangunan::where('ket', 'pengeluaran batu')->orderBy('tanggal')->get();
        $proyek = Proyek::all();
        $namaProyek = Proyek::pluck('nama', 'id_proyek');
        return view('contents.pembangunan.kontruksi.batu', compact('batu', 'proyek', 'namaProyek'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataBatu = [
            'ket' => 'pengeluaran batu',
            'id_proyek' => $request->proyek,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'ukuran' => $request->ukuran,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'harga' => $numericHarga,
            'tot_harga' => $numericTotal
        ];

        $dataProyek = Proyek::where('id_proyek', $request->proyek)->first();
        $namaProyek = 'null';
        if ($dataProyek) {
            $namaProyek = $dataProyek->nama;
        } 

        $exitingBatu = Pembangunan::where('tanggal', $request->tanggal)->where('nama', $request->nama)->where('ukuran', $request->ukuran)->where('deskripsi', $request->deskripsi)
            ->where('jumlah', $request->jumlah)->where('satuan', $request->satuan)->where('harga', $numericHarga)->where('tot_harga', $numericTotal)->where('id_proyek', $request->proyek)
            ->first();

        if ($exitingBatu) {
            $logErrors = 'Proyek: ' . $namaProyek . ' - ' . 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Nama (Barang): ' . $request->nama . ' - ' . 
            'Jumlah: ' . $request->jumlah . ' - ' . 'Satuan: ' . $request->satuan . ' - ' . 'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . 
            ', data tersebut sudah ada di sistem';

            return redirect('batu')->with('logErrors', $logErrors);

        } else {
            Pembangunan::create($dataBatu);
            return redirect('batu');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanBatu = Pembangunan::find($request->id_batu);
        if ($tagihanBatu) {
            $tagihanBatu->id_proyek = $request->proyek;
            $tagihanBatu->tanggal = $request->tanggal;
            $tagihanBatu->nama = $request->nama;
            $tagihanBatu->ukuran = $request->ukuran;
            $tagihanBatu->deskripsi = $request->deskripsi;
            $tagihanBatu->jumlah = $request->jumlah;
            $tagihanBatu->satuan = $request->satuan;
            $tagihanBatu->harga = $numericHarga;
            $tagihanBatu->tot_harga = $numericTotal;

            $tagihanBatu->save();
            return redirect('batu')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('batu');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->delete();
        return redirect('batu');
    }
}
