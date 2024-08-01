<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BBM;
use App\Models\Kendaraan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BBMExport;

class BBMController extends Controller
{
    public function index() {
        $bbm = BBM::orderBy('tanggal', 'asc')->orderBy('id_kendaraan', 'asc')->orderBy('nama', 'asc')->get();
        $periodes = BBM::select(BBM::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        $kendaraan = Kendaraan::all();
        $nopolKendaraan = Kendaraan::pluck('nopol', 'id_kendaraan');
        $merkKendaraan = Kendaraan::pluck('merk', 'id_kendaraan');
        return view('contents.bbm', compact('bbm', 'periodes', 'kendaraan', 'nopolKendaraan', 'merkKendaraan'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotalHarga = preg_replace("/[^0-9]/", "", explode(",", $request->tot_harga)[0]);

        $dataBBM = [
            'nama' => $request->nama,
            'tanggal' => $request->tanggal,
            'id_kendaraan' => $request->kendaraan,
            'jns_bbm' => $request->jns_bbm,
            'liter' => $request->liter,
            'km_awal' => $request->km_awal,
            'km_isi' => $request->km_isi,
            'km_akhir' => $request->km_akhir,
            'km_ltr' => $request->km_ltr,
            'harga' => $numericHarga,
            'tot_harga' => $numericTotalHarga,
            'ket' => $request->ket,
            'tot_km' => $request->tot_km
        ];

        $nopol = null;
        $dataKendaraan = Kendaraan::where('id_kendaraan', $request->kendaraan)->first();
        if ($dataKendaraan) {
            $nopol = $dataKendaraan->nopol;
        }

        $exitingBBM = BBM::where('nama', $request->nama)->where('tanggal', $request->tanggal)->where('id_kendaraan', $request->kendaraan)->where('jns_bbm', $request->jns_bbm)
            ->where('liter', $request->liter)->where('km_awal', $request->km_awal)->where('km_isi', $request->km_isi)->where('km_akhir', $request->km_akhir)
            ->where('km_ltr', $request->km_ltr)->where('harga', $numericHarga)->where('tot_harga', $numericTotalHarga)->where('ket', $request->ket)->where('tot_km', $request->tot_km)
            ->first();

        if ($exitingBBM) {
            $logErrors = 'Nama: ' . $request->nama . ' - ' . 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Nopol / Kode Unit: ' . $nopol . ' - ' . 
            'KM/Liter: ' . $request->km_ltr . ' - ' . 'Total Harga: ' . $request->tot_harga . ' - ' . 'Total KM: ' . $request->tot_km . ' - ' .
            'Ket: ' . $request->ket . ', data tersebut sudah ada di sistem';

            return redirect('bbm')->with('logErrors', $logErrors);

        } else {
            BBM::create($dataBBM);
            return redirect('bbm');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotalHarga = preg_replace("/[^0-9]/", "", explode(",", $request->tot_harga)[0]);

        $dataBBM = BBM::find($request->id_bbm);
        if ($dataBBM) {
            $dataBBM->nama = $request->nama;
            $dataBBM->tanggal = $request->tanggal;
            $dataBBM->id_kendaraan = $request->kendaraan;
            $dataBBM->jns_bbm = $request->jns_bbm;
            $dataBBM->liter = $request->liter;
            $dataBBM->km_awal = $request->km_awal;
            $dataBBM->km_isi = $request->km_isi;
            $dataBBM->km_akhir = $request->km_akhir;
            $dataBBM->km_ltr = $request->km_ltr;
            $dataBBM->harga = $numericHarga;
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

    public function export(Request $request) {
        $mode = $request->metode_export;
        $periode = $request->periode;

        if ($mode == 'all_data') {
            $bbm = BBM::orderBy('tanggal', 'asc')->orderBy('id_kendaraan', 'asc')->orderBy('nama', 'asc')->get();
            return Excel::download(new BBMExport($mode, $bbm), 'Report BBM.xlsx');

        } else {
            $bbm = BBM::whereYear('tanggal', '=', substr($periode, 0, 4))
                ->whereMonth('tanggal', '=', substr($periode, 5, 2))
                ->orderBy('tanggal', 'asc')
                ->orderBy('id_kendaraan', 'asc')
                ->orderBy('nama', 'asc')
                ->get();

            $fileName = 'Report BBM ' . \Carbon\Carbon::parse($periode)->format('M-Y') . '.xlsx';
            return Excel::download(new BBMExport($mode, $bbm), $fileName);
        }
    }
}
