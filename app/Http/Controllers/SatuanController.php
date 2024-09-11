<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Satuan;
use App\Models\Activity;
use Carbon\Carbon;

class SatuanController extends Controller
{
    public function index() {
        $satuan = Satuan::orderBy('nama')->get();
        return view('contents.master_data.satuan', compact('satuan'));
    }

    public function store(Request $request) {
        $existingSatuan = Satuan::where('nama', $request->nama)->first();

        if ($existingSatuan) {
            $logErrors = 'Nama Satuan: ' . $request->nama . ', data tersebut sudah ada di sistem';
            return redirect('satuan')->with('logErrors', $logErrors);

        } else {
            $dataSatuan = Satuan::create(['nama'=> $request->nama]);
            $idSatuan = $dataSatuan->id_satuan;

            // create activity
            $dataActivity = [
                'id_relation' => $idSatuan,
                'description' => 'Menambahkan satuan baru: ' . $request->nama,
                'scope' => 'Satuan',
                'action' => 'Buat Data Baru',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

            return redirect('satuan');
        }
    }

    public function update(Request $request) {
        $dataSatuan = Satuan::find($request->id_satuan);
        if ($dataSatuan) {
            if ($dataSatuan->nama != $request->nama) {
                $checkSatuan = Satuan::where('nama', $request->nama)->exists();
                if ($checkSatuan) {
                    $logErrors = 'Nama Satuan: ' . $request->nama . ', data tersebut sudah ada di sistem';
                    return redirect('satuan')->with('logErrors', $logErrors);
                }
            }

            // create activity
            $dataActivity = [
                'id_relation' => $dataSatuan->id_satuan,
                'description' => 'Update data satuan: ' . $dataSatuan->nama . ' => ' . $request->nama,
                'scope' => 'Satuan',
                'action' => 'Update Data',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

            $dataSatuan->nama = $request->nama;
            $dataSatuan->save();
            return redirect('satuan')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('satuan');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Satuan::whereIn('id_satuan', $validatedIds)->delete();
        return redirect('satuan');
    }
}
