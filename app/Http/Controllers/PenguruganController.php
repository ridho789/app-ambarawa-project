<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembangunan;

class PenguruganController extends Controller
{
    public function index() {
        $pengurugan = Pembangunan::where('ket', 'pengeluaran urug')->orderBy('tanggal')->get();
        return view('contents.pembangunan.pengurugan', compact('pengurugan'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataUrug = [
            'ket' => 'pengeluaran urug',
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'ukuran' => $request->ukuran,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'harga' => $numericHarga,
            'tot_harga' => $numericTotal
        ];

        $exitingUrug = Pembangunan::where('tanggal', $request->tanggal)->where('nama', $request->nama)->where('ukuran', $request->ukuran)->where('deskripsi', $request->deskripsi)
            ->where('jumlah', $request->jumlah)->where('satuan', $request->satuan)->where('harga', $numericHarga)->where('tot_harga', $numericTotal)
            ->first();

        if ($exitingUrug) {
            $logErrors = 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 
            'Jumlah: ' . $request->jumlah . ' - ' . 'Satuan: ' . $request->satuan . ' - ' . 'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . 
            ', data tersebut sudah ada di sistem';

            return redirect('pengurugan')->with('logErrors', $logErrors);

        } else {
            Pembangunan::create($dataUrug);
            return redirect('pengurugan');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanUrug = Pembangunan::find($request->id_pengurugan);
        if ($tagihanUrug) {
            $tagihanUrug->tanggal = $request->tanggal;
            $tagihanUrug->nama = $request->nama;
            $tagihanUrug->ukuran = $request->ukuran;
            $tagihanUrug->deskripsi = $request->deskripsi;
            $tagihanUrug->jumlah = $request->jumlah;
            $tagihanUrug->satuan = $request->satuan;
            $tagihanUrug->harga = $numericHarga;
            $tagihanUrug->tot_harga = $numericTotal;

            $tagihanUrug->save();
            return redirect('pengurugan')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('pengurugan');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->delete();
        return redirect('pengurugan');
    }
}
