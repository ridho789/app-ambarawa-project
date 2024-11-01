<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriMaterial;
use App\Models\Activity;
use Carbon\Carbon;

class KategoriMaterialController extends Controller
{
    public function index() {
        $kategori = KategoriMaterial::orderBy('nama')->get();
        return view('contents.master_data.kategori_material', compact('kategori'));
    }

    public function store(Request $request) {
        $existingKategori = KategoriMaterial::where('nama', $request->nama)->first();

        if ($existingKategori) {
            $logErrors = 'Nama kategori material: ' . $request->nama . ', data tersebut sudah ada di sistem';
            return redirect('kategori_material')->with('logErrors', $logErrors);

        } else {
            $dataKategoriMaterial = KategoriMaterial::create(['nama'=> $request->nama]);
            $idKategoriMaterial = $dataKategoriMaterial->id_kategori;

            // create activity
            $dataActivity = [
                'id_relation' => $idKategoriMaterial,
                'description' => 'Menambahkan kategori material baru: ' . $request->nama,
                'scope' => 'Kategori Material',
                'action' => 'Buat Data Baru',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

            return redirect('kategori_material');
        }
    }

    public function update(Request $request) {
        $dataKategori = KategoriMaterial::find($request->id_kategori);
        if ($dataKategori) {
            if ($dataKategori->nama != $request->nama) {
                $checkKategori = KategoriMaterial::where('nama', $request->nama)->exists();
                if ($checkKategori) {
                    $logErrors = 'Nama kategori material: ' . $request->nama . ', data tersebut sudah ada di sistem';
                    return redirect('kategori_material')->with('logErrors', $logErrors);
                }
            }

            // create activity
            $dataActivity = [
                'id_relation' => $dataKategori->id_kategori,
                'description' => 'Update data kategori material: ' . $dataKategori->nama . ' => ' . $request->nama,
                'scope' => 'Kategori Material',
                'action' => 'Update Data',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

            $dataKategori->nama = $request->nama;
            $dataKategori->save();

            return redirect('kategori_material?page=' . $request->page)->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('kategori_material');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        KategoriMaterial::whereIn('id_kategori', $validatedIds)->delete();
        return redirect('kategori_material');
    }
}
