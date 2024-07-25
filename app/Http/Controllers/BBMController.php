<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BBM;
use App\Models\TagihanAMB;

class BBMController extends Controller
{
    public function index() {
        $bbm = BBM::orderBy('nama', 'asc')->get();
        return view('contents.bbm', compact('bbm'));
    }

    public function store(Request $request) {
        $numericTotalHarga = preg_replace("/[^0-9]/", "", explode(",", $request->tot_harga)[0]);

        $dataBBM = [
            'nama' => $request->nama,
            'tanggal' => $request->tanggal,
            'kode_unit' => $request->kode_unit,
            'nopol' => $request->nopol,
            'jns_mobil' => $request->jns_mobil,
            'jns_bbm' => $request->jns_bbm,
            'liter' => $request->liter,
            'km_awal' => $request->km_awal,
            'km_isi' => $request->km_isi,
            'km_akhir' => $request->km_akhir,
            'km_ltr' => $request->km_ltr,
            'tot_harga' => $numericTotalHarga,
            'ket' => $request->ket,
            'tot_km' => $request->tot_km
        ];

        $exitingBBM = BBM::where('nama', $request->nama)->where('tanggal', $request->tanggal)->where('kode_unit', $request->kode_unit)
            ->where('nopol', $request->nopol)->where('jns_mobil', $request->jns_mobil)->where('jns_bbm', $request->jns_bbm)
            ->where('liter', $request->liter)->where('km_awal', $request->km_awal)->where('km_isi', $request->km_isi)->where('km_akhir', $request->km_akhir)
            ->where('km_ltr', $request->km_ltr)->where('tot_harga', $numericTotalHarga)->where('ket', $request->ket)->where('tot_km', $request->tot_km)
            ->first();

        if ($exitingBBM) {
            $logErrors = 'Nama: ' . $request->nama . ' - ' . 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Kode Unit: ' . $request->kode_unit . ' - ' . 
            'Nopol: ' . $request->nopol . ' - ' . 'KM/Liter: ' . $request->km_ltr . ' - ' . 'Total Harga: ' . $request->tot_harga . ' - ' . 'Total KM: ' . $request->tot_km . ' - ' .
            'Ket: ' . $request->ket . ', data tersebut sudah ada di sistem';

            return redirect('bbm')->with('logErrors', $logErrors);

        } else {
            BBM::create($dataBBM);
            return redirect('bbm');
        }
    }

    public function update(Request $request) {
        $numericTotalHarga = preg_replace("/[^0-9]/", "", explode(",", $request->tot_harga)[0]);

        $dataBBM = BBM::find($request->id_bbm);
        if ($dataBBM) {
            $dataBBM->nama = $request->nama;
            $dataBBM->tanggal = $request->tanggal;
            $dataBBM->kode_unit = $request->kode_unit;
            $dataBBM->nopol = $request->nopol;
            $dataBBM->jns_mobil = $request->jns_mobil;
            $dataBBM->jns_bbm = $request->jns_bbm;
            $dataBBM->liter = $request->liter;
            $dataBBM->km_awal = $request->km_awal;
            $dataBBM->km_isi = $request->km_isi;
            $dataBBM->km_akhir = $request->km_akhir;
            $dataBBM->km_ltr = $request->km_ltr;
            $dataBBM->tot_harga = $numericTotalHarga;
            $dataBBM->ket = $request->ket;
            $dataBBM->tot_km = $request->tot_km;

            $dataBBM->save();
            return redirect('bbm')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('bbm');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        BBM::whereIn('id_bbm', $validatedIds)->delete();
        return redirect('bbm');
    }
}
