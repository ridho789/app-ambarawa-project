<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriMaterial;

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
            KategoriMaterial::insert(['nama'=> $request->nama]);
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

            $dataKategori->nama = $request->nama;
            $dataKategori->save();
            return redirect('kategori_material')->with('success', 'Data berhasil diperbaharui!');
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
