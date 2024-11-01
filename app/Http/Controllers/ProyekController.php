<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Pembangunan;
use App\Models\Activity;
use Carbon\Carbon;

class ProyekController extends Controller
{
    public function index() {
        $proyek = Proyek::orderBy('nama')->get();
        foreach ($proyek as $p) {
            $p->totalUrug = Pembangunan::where('ket', 'pengeluaran urug')
                ->where('id_proyek', $p->id_proyek)
                ->sum('tot_harga');

            $p->totalBesi = Pembangunan::where('ket', 'pengeluaran besi')
                ->where('id_proyek', $p->id_proyek)
                ->sum('tot_harga');

            $p->totalMaterial = Pembangunan::whereNotNull('id_kategori')
                ->where('id_proyek', $p->id_proyek)
                ->sum('tot_harga');
        }
        
        return view('contents.master_data.proyek', compact('proyek'));
    }

    public function store(Request $request) {
        $existingProyek = Proyek::where('nama', $request->nama)->where('subproyek', $request->subproyek)->first();

        if ($existingProyek) {
            $logErrors = 'Nama proyek: ' . $request->nama . ' - ' . 'Subproyek: ' . $request->subproyek . ', data tersebut sudah ada di sistem';
            return redirect('proyek')->with('logErrors', $logErrors);

        } else {
            $dataProyek = Proyek::create(['nama' => $request->nama, 'subproyek' => $request->subproyek]);
            $idProyek = $dataProyek->id_proyek;

            // create activity
            $dataActivity = [
                'id_relation' => $idProyek,
                'description' => 'Menambahkan proyek baru: ' . $request->nama,
                'scope' => 'Kendaraan',
                'action' => 'Buat Data Baru',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

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

            // create activity
            $dataActivity = [
                'id_relation' => $dataProyek->id_proyek,
                'description' => 'Update data proyek: ' . $dataProyek->nama . ' => ' . $request->nama,
                'scope' => 'Proyek',
                'action' => 'Update Data',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

            $dataProyek->nama = $request->nama;
            $dataProyek->subproyek = $request->subproyek;
            $dataProyek->save();
            return redirect('proyek?page=' . $request->page)->with('success', 'Data berhasil diperbaharui!');
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
