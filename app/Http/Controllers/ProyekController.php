<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Pembangunan;

class ProyekController extends Controller
{
    public function index() {
        $proyek = Proyek::orderBy('nama')->get();
        $totalUrug = Pembangunan::where('ket', 'pengeluaran urug')->sum('tot_harga');
        $totalBesi = Pembangunan::where('ket', 'pengeluaran besi')->sum('tot_harga');
        $totalMaterial = Pembangunan::whereNotNull('id_kategori')->sum('tot_harga');
        return view('contents.master_data.proyek', compact('proyek', 'totalUrug', 'totalBesi', 'totalMaterial'));
    }

    public function store(Request $request) {
        $existingProyek = Proyek::where('nama', $request->nama)->where('subproyek', $request->subproyek)->first();

        if ($existingProyek) {
            $logErrors = 'Nama proyek: ' . $request->nama . ' - ' . 'Subproyek: ' . $request->subproyek . ', data tersebut sudah ada di sistem';
            return redirect('proyek')->with('logErrors', $logErrors);

        } else {
            Proyek::insert(['nama' => $request->nama, 'subproyek' => $request->subproyek]);
            return redirect('proyek');
        }
    }

    public function update(Request $request) {
        $dataProyek = Proyek::find($request->id_proyek);
        if ($dataProyek) {
            if ($dataProyek->nama != $request->nama) {
                $checkProyek = Proyek::where('nama', $request->nama)->where('subproyek', $request->subproyek)->exists();
                if ($checkProyek) {
                    $logErrors = 'Nama proyek: ' . $request->nama . ' - ' . 'Subproyek: ' . $request->subproyek . ', data tersebut sudah ada di sistem';
                    return redirect('proyek')->with('logErrors', $logErrors);
                }
            }

            $dataProyek->nama = $request->nama;
            $dataProyek->subproyek = $request->subproyek;
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
