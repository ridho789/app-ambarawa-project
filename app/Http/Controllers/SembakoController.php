<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sembako;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SembakoExport;

class SembakoController extends Controller
{
    public function index() {
        $sembako = Sembako::orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
        $periodes = Sembako::select(Sembako::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        return view('contents.sembako', compact('sembako', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataSembako = [
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'qty' => $request->qty,
            'unit' => $request->unit,
            'harga' => $numericHarga,
            'total' => $numericTotal
        ];

        $exitingSembako = Sembako::where('tanggal', $request->tanggal)->where('nama', $request->nama)
            ->where('qty', $request->qty)->where('unit', $request->unit)->where('harga', $numericHarga)->where('total', $numericTotal)
            ->first();

        if ($exitingSembako) {
            $logErrors = 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 
            'Jumlah: ' . $request->qty . ' - ' . 'Satuan: ' . $request->unit . ' - ' . 'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . 
            ', data tersebut sudah ada di sistem';

            return redirect('sembako')->with('logErrors', $logErrors);

        } else {
            Sembako::create($dataSembako);
            return redirect('sembako');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanSembako = Sembako::find($request->id_sembako);
        if ($tagihanSembako) {
            $tagihanSembako->tanggal = $request->tanggal;
            $tagihanSembako->nama = $request->nama;
            $tagihanSembako->qty = $request->qty;
            $tagihanSembako->unit = $request->unit;
            $tagihanSembako->harga = $numericHarga;
            $tagihanSembako->total = $numericTotal;

            $tagihanSembako->save();
            return redirect('sembako')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('sembako');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Sembako::whereIn('id_sembako', $validatedIds)->delete();
        return redirect('sembako');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $periode = $request->periode;

        if ($mode == 'all_data') {
            $sembako = Sembako::orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
            return Excel::download(new SembakoExport($mode, $sembako), 'Report Sembako.xlsx');

        } else {
            $sembako = Sembako::whereYear('tanggal', '=', substr($periode, 0, 4))
                ->whereMonth('tanggal', '=', substr($periode, 5, 2))
                ->orderBy('tanggal', 'asc')
                ->orderBy('nama', 'asc')
                ->get();

            $fileName = 'Report Sembako ' . \Carbon\Carbon::parse($periode)->format('M-Y') . '.xlsx';
            return Excel::download(new SembakoExport($mode, $sembako), $fileName);
        }
    }
}
