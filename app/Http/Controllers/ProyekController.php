<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;

class ProyekController extends Controller
{
    public function index() {
        $proyek = Proyek::orderBy('nama')->get();
        return view('contents.master_data.proyek', compact('proyek'));
    }

    public function store(Request $request) {
        $existingProyek = Proyek::where('nama', $request->nama)->first();

        if ($existingProyek) {
            $logErrors = 'Nama proyek: ' . $request->nama . ', data tersebut sudah ada di sistem';
            return redirect('proyek')->with('logErrors', $logErrors);

        } else {
            Proyek::insert(['nama'=> $request->nama]);
            return redirect('proyek');
        }
    }

    public function update(Request $request) {
        $dataProyek = Proyek::find($request->id_proyek);
        if ($dataProyek) {
            if ($dataProyek->nama != $request->nama) {
                $checkProyek = Proyek::where('nama', $request->nama)->exists();
                if ($checkProyek) {
                    $logErrors = 'Nama proyek: ' . $request->nama . ', data tersebut sudah ada di sistem';
                    return redirect('proyek')->with('logErrors', $logErrors);
                }
            }

            $dataProyek->nama = $request->nama;
            $dataProyek->save();
            return redirect('proyek')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('proyek');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Proyek::whereIn('id_proyek', $validatedIds)->delete();
        return redirect('proyek');
    }
}
