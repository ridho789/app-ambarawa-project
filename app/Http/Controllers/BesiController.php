<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembangunan;
use App\Models\Proyek;

class BesiController extends Controller
{
    public function index() {
        $besi = Pembangunan::where('ket', 'pengeluaran besi')->orderBy('tanggal')->get();
        $proyek = Proyek::all();
        $namaProyek = Proyek::pluck('nama', 'id_proyek');
        return view('contents.pembangunan.kontruksi.besi', compact('besi', 'proyek', 'namaProyek'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataBesi = [
            'ket' => 'pengeluaran besi',
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

        $exitingBesi = Pembangunan::where('tanggal', $request->tanggal)->where('nama', $request->nama)->where('ukuran', $request->ukuran)->where('deskripsi', $request->deskripsi)
            ->where('jumlah', $request->jumlah)->where('satuan', $request->satuan)->where('harga', $numericHarga)->where('tot_harga', $numericTotal)->where('id_proyek', $request->proyek)
            ->first();

        if ($exitingBesi) {
            $logErrors = 'Proyek: ' . $namaProyek . ' - ' . 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Nama (Barang): ' . $request->nama . ' - ' . 
            'Jumlah: ' . $request->jumlah . ' - ' . 'Satuan: ' . $request->satuan . ' - ' . 'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . 
            ', data tersebut sudah ada di sistem';

            return redirect('besi')->with('logErrors', $logErrors);

        } else {
            Pembangunan::create($dataBesi);
            return redirect('besi');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanBesi = Pembangunan::find($request->id_besi);
        if ($tagihanBesi) {
            $tagihanBesi->id_proyek = $request->proyek;
            $tagihanBesi->tanggal = $request->tanggal;
            $tagihanBesi->nama = $request->nama;
            $tagihanBesi->ukuran = $request->ukuran;
            $tagihanBesi->deskripsi = $request->deskripsi;
            $tagihanBesi->jumlah = $request->jumlah;
            $tagihanBesi->satuan = $request->satuan;
            $tagihanBesi->harga = $numericHarga;
            $tagihanBesi->tot_harga = $numericTotal;

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
}