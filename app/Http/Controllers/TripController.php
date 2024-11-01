<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Kendaraan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TripExport;
use Carbon\Carbon;
use DateTime;

class TripController extends Controller
{
    public function index() {
        $trips = Trip::orderBy('tanggal', 'asc')->orderBy('id_kendaraan', 'asc')->orderBy('kota', 'asc')->get();
        $periodes = Trip::select(Trip::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        $kendaraan = Kendaraan::orderBy('nopol')->get();
        $nopolKendaraan = Kendaraan::pluck('nopol', 'id_kendaraan');
        $merkKendaraan = Kendaraan::pluck('merk', 'id_kendaraan');
        return view('contents.trip', compact('trips', 'periodes', 'kendaraan', 'nopolKendaraan', 'merkKendaraan'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        // File
        $request->validate([
            'file' => 'mimes:pdf,png,jpeg,jpg|max:2048',
        ]);

        $filePath = null;
        if ($request->file('file')) {
            $file = $request->file('file');
            $dateTime = new DateTime();
            $dateTime->modify('+7 hours');
            $currentDateTime = $dateTime->format('d_m_Y_H_i_s');
            $fileName = $currentDateTime . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('Trip', $fileName, 'public');
        }

        $dataTrip = [
            'tanggal' => $request->tanggal,
            'kota' => $request->kota,
            'nama' => $request->nama,
            'ket' => $request->ket,
            'uraian' => $request->uraian,
            'id_kendaraan' => $request->kendaraan,
            'qty' => $request->qty,
            'unit' => $request->unit,
            'km_awal' => $request->km_awal,
            'km_isi_seb' => $request->km_isi_seb,
            'km_isi_sek' => $request->km_isi_sek,
            'km_akhir' => $request->km_akhir,
            'km_ltr' => $request->km_ltr,
            'harga' => $numericHarga,
            'total' => $numericTotal,
            'file' => $filePath
        ];

        $nopol = null;
        $dataKendaraan = Kendaraan::where('id_kendaraan', $request->kendaraan)->first();
        if ($dataKendaraan) {
            $nopol = $dataKendaraan->nopol;
        }

        $exitingTrip = Trip::where('tanggal', $request->tanggal)
            ->where('kota', $request->kota)
            ->where('nama', $request->nama)
            ->where('km_isi_seb', $request->km_isi_seb)
            ->where('km_isi_sek', $request->km_isi_sek)
            ->where('ket', $request->ket)
            ->where('uraian', $request->uraian)
            ->where('id_kendaraan', $request->kendaraan)
            ->where('qty', $request->qty)
            ->where('unit', $request->unit)
            ->where('km_awal', $request->km_awal)
            ->where('km_akhir', $request->km_akhir)
            ->where('km_ltr', $request->km_ltr)
            ->where('harga', $numericHarga)
            ->where('total', $numericTotal)
            ->first();

        if ($exitingTrip) {
            $logErrors = 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 
                'Kota: ' . $request->kota . ' - ' . 
                'Nama: ' . $request->nama . ' - ' . 
                'Keterangan: ' . $request->ket . ' - ' . 
                'Uraian: ' . $request->uraian . ' - ' . 
                'Nopol / Kode Unit: ' . $nopol . ' - ' . 
                'Qty: ' . $request->qty . ' - ' . 
                'Unit: ' . $request->unit . ' - ' . 
                'Harga: ' . $request->harga . ' - ' . 
                'Total Harga: ' . $request->total . 
                ', data tersebut sudah ada di sistem';

            return redirect('trip')->with('logErrors', $logErrors);

        } else {
            Trip::create($dataTrip);
            return redirect('trip');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        // File
        $request->validate([
            'file' => 'mimes:pdf,png,jpeg,jpg|max:2048',
        ]);

        $filePath = null;
        if ($request->file('file')) {
            $file = $request->file('file');
            $dateTime = new DateTime();
            $dateTime->modify('+7 hours');
            $currentDateTime = $dateTime->format('d_m_Y_H_i_s');
            $fileName = $currentDateTime . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('Trip', $fileName, 'public');
        }
        
        $tagihanTrip = Trip::find($request->id_trip);
        if ($tagihanTrip) {
            $tagihanTrip->tanggal = $request->tanggal;
            $tagihanTrip->kota = $request->kota;
            $tagihanTrip->nama = $request->nama;
            $tagihanTrip->ket = $request->ket;
            $tagihanTrip->uraian = $request->uraian;
            $tagihanTrip->id_kendaraan = $request->kendaraan;
            $tagihanTrip->qty = $request->qty;
            $tagihanTrip->unit = $request->unit;
            $tagihanTrip->km_awal = $request->km_awal;
            $tagihanTrip->km_isi_seb = $request->km_isi_seb;
            $tagihanTrip->km_isi_sek = $request->km_isi_sek;
            $tagihanTrip->km_akhir = $request->km_akhir;
            $tagihanTrip->km_ltr = $request->km_ltr;
            $tagihanTrip->harga = $numericHarga;
            $tagihanTrip->total = $numericTotal;
            
            if ($filePath) {
                $tagihanTrip->file = $filePath;
            }

            $tagihanTrip->save();
            return redirect('trip?page=' . $request->page)->with('success', 'Data berhasil diperbaharui!');
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
            $trip = Trip::orderBy('tanggal', 'asc')->orderBy('id_kendaraan', 'asc')->orderBy('kota', 'asc')->get();
            return Excel::download(new TripExport($mode, $trip, $rangeDate), 'Report Trip.xlsx');

        } else {
            $trip = Trip::where('tanggal', '>=', $start_date)
                ->where('tanggal', '<=', $end_date)
                ->orderBy('nama', 'asc')
                ->orderBy('tanggal', 'asc')
                ->orderBy('id_kendaraan', 'asc')
                ->orderBy('kota', 'asc')
                ->get();

            $fileName = 'Report Trip ' . $rangeDate . '.xlsx';
            return Excel::download(new TripExport($mode, $trip, $rangeDate), $fileName);
        }
    }
    
    // Update status
    public function pending(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Trip::whereIn('id_trip', $validatedIds)->update([
            'status' => 'pending'
        ]);
        return redirect('trip');
    }

    public function process(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Trip::whereIn('id_trip', $validatedIds)->update([
            'status' => 'processing'
        ]);
        return redirect('trip');
    }

    public function paid(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Trip::whereIn('id_trip', $validatedIds)->update([
            'status' => 'paid'
        ]);
        return redirect('trip');
    }
}
