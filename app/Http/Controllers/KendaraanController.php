<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;

class KendaraanController extends Controller
{
    public function index() {
        $kendaraan = Kendaraan::orderBy('nopol')->get();
        return view('contents.master_data.kendaraan', compact('kendaraan'));
    }

    public function store(Request $request) {
        $existingKendaraan = Kendaraan::where('nopol', $request->nopol)->first();

        if ($existingKendaraan) {
            $logErrors = 'Nopol Kendaraan: ' . $request->nopol . ', data tersebut sudah ada di sistem';
            return redirect('kendaraan')->with('logErrors', $logErrors);

        } else {
            Kendaraan::insert(['nopol'=> $request->nopol, 'merk'=> $request->merk, 'jns_bbm'=> $request->jns_bbm]);
            return redirect('kendaraan');
        }
    }

    public function update(Request $request) {
        $dataKendaraan = Kendaraan::find($request->id_kendaraan);
        if ($dataKendaraan) {
            if ($dataKendaraan->nopol != $request->nopol) {
                $checkKendaraan = Kendaraan::where('nopol', $request->nopol)->exists();
                if ($checkKendaraan) {
                    $logErrors = 'Nopol Kendaraan: ' . $request->nopol . ', data tersebut sudah ada di sistem';
                    return redirect('kendaraan')->with('logErrors', $logErrors);
                }
            }

            $dataKendaraan->nopol = $request->nopol;
            $dataKendaraan->merk = $request->merk;
            $dataKendaraan->jns_bbm = $request->jns_bbm;
            $dataKendaraan->save();
            return redirect('kendaraan')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('kendaraan');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Kendaraan::whereIn('id_kendaraan', $validatedIds)->delete();
        return redirect('kendaraan');
    }
}
