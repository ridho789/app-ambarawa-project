<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Activity;
use Carbon\Carbon;

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
            $dataKendaraan = Kendaraan::create(['nopol'=> $request->nopol, 'merk'=> $request->merk, 'jns_bbm'=> $request->jns_bbm]);
            $idKendaraan = $dataKendaraan->id_kendaraan;

            // create activity
            $dataActivity = [
                'id_relation' => $idKendaraan,
                'description' => 'Menambahkan kendaraan baru: ' . $request->nopol,
                'scope' => 'Kendaraan',
                'action' => 'Buat Data Baru',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

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

            // create activity
            $dataActivity = [
                'id_relation' => $dataKendaraan->id_kendaraan,
                'description' => 'Update data kendaraan: ' . $dataKendaraan->nopol . ' => ' . $request->nopol,
                'scope' => 'Kendaraan',
                'action' => 'Update Data',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

            $dataKendaraan->nopol = $request->nopol;
            $dataKendaraan->merk = $request->merk;
            $dataKendaraan->jns_bbm = $request->jns_bbm;
            $dataKendaraan->save();
            return redirect('kendaraan?page=' . $request->page)->with('success', 'Data berhasil diperbaharui!');
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
