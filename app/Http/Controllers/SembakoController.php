<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sembako;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SembakoExport;
use Carbon\Carbon;

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

        // Ambil input tanggal dari request
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        $rangeDate = null;

        // Format bulan dan tahun untuk perbandingan
        $start_month_year = $start_date->format('m-Y');
        $end_month_year = $end_date->format('m-Y');

        // Format tahun untuk perbandingan
        $start_year = $start_date->format('Y');
        $end_year = $end_date->format('Y');

        if ($start_date->isSameDay($end_date)) {
            // Format tanggal yang diinginkan jika tanggal sama
            $rangeDate = $start_date->format('d M Y');

        } elseif ($start_month_year === $end_month_year) {
            // Format tanggal yang diinginkan jika bulan dan tahun sama
            $start_day = $start_date->format('d');
            $end_day = $end_date->format('d');
            $month_year = $start_date->format('M Y');
            $rangeDate = "{$start_day} - {$end_day} {$month_year}";

        } elseif ($start_year === $end_year) {
            // Format tanggal yang diinginkan jika tahun sama tetapi bulan berbeda
            $start_day = $start_date->format('d');
            $end_day = $end_date->format('d');
            $start_month = $start_date->format('M');
            $end_month = $end_date->format('M');
            $year = $start_date->format('Y');
            $rangeDate = "{$start_day} {$start_month} - {$end_day} {$end_month} {$year}";

        } else {
            $rangeDate = "{$start_date->format('d M Y')} - {$end_date->format('d M Y')}";
        }

        if ($mode == 'all_data') {
            $sembako = Sembako::orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
            return Excel::download(new SembakoExport($mode, $sembako), 'Report Sembako.xlsx');

        } else {
            $sembako = Sembako::where('tanggal', '>=', $start_date)
                ->where('tanggal', '<=', $end_date)
                ->orderBy('tanggal', 'asc')
                ->orderBy('nama', 'asc')
                ->get();

            $fileName = 'Report Sembako ' . $rangeDate . '.xlsx';
            return Excel::download(new SembakoExport($mode, $sembako), $fileName);
        }
    }
}
