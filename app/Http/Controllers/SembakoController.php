<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sembako;

class SembakoController extends Controller
{
    public function index() {
        $sembako = Sembako::orderBy('tanggal', 'asc')->get();
        return view('contents.sembako', compact('sembako'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataSembako = [
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'qty' => $request->qty,
            'unit' => $request->unit,
            'harga' => $numericHarga,
            'total' => $numericTotal
        ];

        $exitingSembako = Sembako::where('tanggal', $request->tanggal)->where('nama', $request->nama)
            ->where('qty', $request->qty)->where('unit', $request->unit)->where('harga', $numericHarga)->where('total', $numericTotal)
            ->first();

        if ($exitingSembako) {
            $logErrors = 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 
            'Jumlah: ' . $request->qty . ' - ' . 'Satuan: ' . $request->unit . ' - ' . 'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . 
            ', data tersebut sudah ada di sistem';

            return redirect('sembako')->with('logErrors', $logErrors);

        } else {
            Sembako::create($dataSembako);
            return redirect('sembako');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanSembako = Sembako::find($request->id_sembako);
        if ($tagihanSembako) {
            $tagihanSembako->tanggal = $request->tanggal;
            $tagihanSembako->nama = $request->nama;
            $tagihanSembako->qty = $request->qty;
            $tagihanSembako->unit = $request->unit;
            $tagihanSembako->harga = $numericHarga;
            $tagihanSembako->total = $numericTotal;

            $tagihanSembako->save();
            return redirect('sembako')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('sembako');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Sembako::whereIn('id_sembako', $validatedIds)->delete();
        return redirect('sembako');
    }
}
