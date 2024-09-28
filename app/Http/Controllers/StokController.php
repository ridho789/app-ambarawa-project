<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Imports\StokImport;
use App\Exports\StokBarangExport;
use App\Models\StokBarang;
use App\Models\BarangMasuk;
use App\Models\Satuan;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DateTime;

class StokController extends Controller
{
    public function index() {
        $stok = StokBarang::orderBy('nama')->get();
        $satuan = Satuan::all();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        return view('contents.inventaris.stok_barang', compact('stok', 'satuan', 'namaSatuan'));
    }

    public function update(Request $request) {
        $request->validate([
            'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);
        
        $dataStok = StokBarang::find($request->id_stok);
        if ($dataStok) {
            $dataStok->nama = $request->nama;
            $dataStok->no_rak = $request->no_rak;
            $dataStok->kategori = $request->kategori;
            $dataStok->merk = $request->merk;
            $dataStok->type = $request->type;
            $dataStok->jumlah = $request->jumlah;
            $dataStok->id_satuan = $request->unit;
            $dataStok->keterangan = $request->keterangan;

            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($dataStok->foto && Storage::exists('public/' . $dataStok->foto)) {
                    Storage::delete('public/' . $dataStok->foto);
                }
        
                // Simpan foto baru
                $file = $request->file('foto');
                $dateTime = new DateTime();
                $dateTime->modify('+7 hours');
                $currentDateTime = $dateTime->format('d_m_Y_H_i_s');
                $fileName = $currentDateTime . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('Stok Barang', $fileName, 'public');

                if ($filePath) {
                    $dataStok->foto = $filePath;
                }
            }
            
            $dataStok->save();

            // create update activity
            $dataActivity = [
                'id_relation' => $dataStok->id_stok_barang,
                'description' => 'Perbaharui Data Stok Barang: ' . $dataStok->nama,
                'scope' => 'Stok Barang',
                'action' => 'Update Data',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);
            return redirect('stok')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('stok');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        StokBarang::whereIn('id_stok_barang', $validatedIds)->delete();
        return redirect('stok');
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);
    
        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file);
            $sheetNames = $spreadsheet->getSheetNames();
    
            $import = new StokImport($sheetNames);
            Excel::import($import, $file);
            $logErrors = $import->getLogErrors();

            if ($logErrors) {
                return redirect('stok')->with('logErrors', $logErrors);
            } else {
                return redirect('stok');
            }
    
        } catch (\Exception $e) {
            $sqlErrors = $e->getMessage();
    
            if (!empty($sqlErrors)){
                $logErrors = $sqlErrors;
            }
    
            return redirect('stok')->with('logErrors', $logErrors);
        }
    }

    public function export() {
        $StokBarang = StokBarang::orderBy('nama', 'asc')->get();
        return Excel::download(new StokBarangExport($StokBarang), 'Report Stok Barang.xlsx');
    }
}
