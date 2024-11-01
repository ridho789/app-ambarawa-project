<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\Activity;
use Carbon\Carbon;

class TokoController extends Controller
{
    public function index() {
        $toko = Toko::orderBy('nama')->get();
        return view('contents.master_data.toko', compact('toko'));
    }

    public function store(Request $request) {
        $existingToko = Toko::where('nama', $request->nama)->first();

        if ($existingToko) {
            $logErrors = 'Nama Toko: ' . $request->nama . ', data tersebut sudah ada di sistem';
            return redirect('toko')->with('logErrors', $logErrors);

        } else {
            $dataToko = Toko::create(['nama'=> $request->nama]);
            $idToko = $dataToko->id_toko;

            // create activity
            $dataActivity = [
                'id_relation' => $idToko,
                'description' => 'Menambahkan toko baru: ' . $request->nama,
                'scope' => 'Toko',
                'action' => 'Buat Data Baru',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

            return redirect('toko');
        }
    }

    public function update(Request $request) {
        $dataToko = Toko::find($request->id_toko);
        if ($dataToko) {
            if ($dataToko->nama != $request->nama) {
                $checkToko = Toko::where('nama', $request->nama)->exists();
                if ($checkToko) {
                    $logErrors = 'Nama Toko: ' . $request->nama . ', data tersebut sudah ada di sistem';
                    return redirect('toko')->with('logErrors', $logErrors);
                }
            }

            // create activity
            $dataActivity = [
                'id_relation' => $dataToko->id_toko,
                'description' => 'Update data toko: ' . $dataToko->nama . ' => ' . $request->nama,
                'scope' => 'Toko',
                'action' => 'Update Data',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

            $dataToko->nama = $request->nama;
            $dataToko->save();
            return redirect('toko?page=' . $request->page)->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('toko');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Toko::whereIn('id_toko', $validatedIds)->delete();
        return redirect('toko');
    }
}
