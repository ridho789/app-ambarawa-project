<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TripExport;

class TripController extends Controller
{
    public function index() {
        $trips = Trip::orderBy('tanggal', 'asc')->orderBy('nopol', 'asc')->orderBy('kota', 'asc')->get();
        $periodes = Trip::select(Trip::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        return view('contents.trip', compact('trips', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataTrip = [
            'tanggal' => $request->tanggal,
            'kota' => $request->kota,
            'ket' => $request->ket,
            'uraian' => $request->uraian,
            'nopol' => $request->nopol,
            'merk' => $request->merk,
            'qty' => $request->qty,
            'unit' => $request->unit,
            'km_awal' => $request->km_awal,
            'km_isi' => $request->km_isi,
            'km_akhir' => $request->km_akhir,
            'km_ltr' => $request->km_ltr,
            'harga' => $numericHarga,
            'total' => $numericTotal
        ];

        $exitingTrip = Trip::where('tanggal', $request->tanggal)->where('kota', $request->kota)->where('ket', $request->ket)
            ->where('uraian', $request->uraian)->where('nopol', $request->nopol)->where('merk', $request->merk)->where('qty', $request->qty)
            ->where('unit', $request->unit)->where('km_awal', $request->km_awal)->where('km_isi', $request->km_isi)->where('km_akhir', $request->km_akhir)
            ->where('km_ltr', $request->km_ltr)->where('harga', $numericHarga)->where('total', $numericTotal)
            ->first();

        if ($exitingTrip) {
            $logErrors = 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Kota: ' . $request->kota . ' - ' . 'Ket: ' . $request->ket . ' - ' . 'Uraian: ' . $request->uraian . ' - ' . 
            'Nopol: ' . $request->nopol . ' - ' . 'Merk: ' . $request->merk . ' - ' . 'Qty: ' . $request->qty . ' - ' . 'Unit: ' . $request->unit . ' - ' . 
            'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . ', data tersebut sudah ada di sistem';

            return redirect('trip')->with('logErrors', $logErrors);

        } else {
            Trip::create($dataTrip);
            return redirect('trip');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanTrip = Trip::find($request->id_trip);
        if ($tagihanTrip) {
            $tagihanTrip->tanggal = $request->tanggal;
            $tagihanTrip->kota = $request->kota;
            $tagihanTrip->ket = $request->ket;
            $tagihanTrip->uraian = $request->uraian;
            $tagihanTrip->nopol = $request->nopol;
            $tagihanTrip->merk = $request->merk;
            $tagihanTrip->qty = $request->qty;
            $tagihanTrip->unit = $request->unit;
            $tagihanTrip->km_awal = $request->km_awal;
            $tagihanTrip->km_isi = $request->km_isi;
            $tagihanTrip->km_akhir = $request->km_akhir;
            $tagihanTrip->km_ltr = $request->km_ltr;
            $tagihanTrip->harga = $numericHarga;
            $tagihanTrip->total = $numericTotal;

            $tagihanTrip->save();
            return redirect('trip')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('trip');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Trip::whereIn('id_trip', $validatedIds)->delete();
        return redirect('trip');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $periode = $request->periode;

        if ($mode == 'all_data') {
            $trip = Trip::orderBy('tanggal', 'asc')->orderBy('nopol', 'asc')->orderBy('kota', 'asc')->get();
            return Excel::download(new TripExport($mode, $trip), 'Report Trip.xlsx');

        } else {
            $trip = Trip::whereYear('tanggal', '=', substr($periode, 0, 4))
                ->whereMonth('tanggal', '=', substr($periode, 5, 2))
                ->orderBy('tanggal', 'asc')
                ->orderBy('nopol', 'asc')
                ->orderBy('kota', 'asc')
                ->get();

            $fileName = 'Report Trip ' . \Carbon\Carbon::parse($periode)->format('M-Y') . '.xlsx';
            return Excel::download(new TripExport($mode, $trip), $fileName);
        }
    }
}
