<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BBM;
use App\Models\Kendaraan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BBMExport;
use Carbon\Carbon;
use DateTime;

class BBMController extends Controller
{
    public function index() {
        $bbm = BBM::orderBy('tanggal', 'asc')->orderBy('id_kendaraan', 'asc')->get();
        $periodes = BBM::select(BBM::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        $kendaraan = Kendaraan::all();
        $nopolKendaraan = Kendaraan::pluck('nopol', 'id_kendaraan');
        $merkKendaraan = Kendaraan::pluck('merk', 'id_kendaraan');
        $bbmKendaraan = Kendaraan::pluck('jns_bbm', 'id_kendaraan');
        return view('contents.bbm', compact('bbm', 'periodes', 'kendaraan', 'nopolKendaraan', 'merkKendaraan', 'bbmKendaraan'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotalHarga = preg_replace("/[^0-9]/", "", explode(",", $request->tot_harga)[0]);

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
            $filePath = $file->storeAs('BBM', $fileName, 'public');
        }

        $dataBBM = [
            'nama' => $request->nama,
            'tanggal' => $request->tanggal,
            'id_kendaraan' => $request->kendaraan,
            'liter' => $request->liter,
            'km_awal' => $request->km_awal,
            'km_isi_seb' => $request->km_isi_seb,
            'km_isi_sek' => $request->km_isi_sek,
            'km_akhir' => $request->km_akhir,
            'km_ltr' => $request->km_ltr,
            'harga' => $numericHarga,
            'tot_harga' => $numericTotalHarga,
            'ket' => $request->ket,
            'tot_km' => $request->tot_km,
            'file' => $filePath
        ];

        $nopol = null;
        $dataKendaraan = Kendaraan::where('id_kendaraan', $request->kendaraan)->first();
        if ($dataKendaraan) {
            $nopol = $dataKendaraan->nopol;
        }

        $exitingBBM = BBM::where('nama', $request->nama)->where('tanggal', $request->tanggal)->where('id_kendaraan', $request->kendaraan)
            ->where('liter', $request->liter)->where('km_awal', $request->km_awal)->where('km_isi_seb', $request->km_isi_seb)->where('km_isi_sek', $request->km_isi_sek)
            ->where('km_akhir', $request->km_akhir)->where('km_ltr', $request->km_ltr)->where('harga', $numericHarga)->where('tot_harga', $numericTotalHarga)
            ->where('ket', $request->ket)->where('tot_km', $request->tot_km)
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
            $filePath = $file->storeAs('BBM', $fileName, 'public');
        }

        $dataBBM = BBM::find($request->id_bbm);
        if ($dataBBM) {
            $dataBBM->nama = $request->nama;
            $dataBBM->tanggal = $request->tanggal;
            $dataBBM->id_kendaraan = $request->kendaraan;
            $dataBBM->liter = $request->liter;
            $dataBBM->km_awal = $request->km_awal;
            $dataBBM->km_isi_seb = $request->km_isi_seb;
            $dataBBM->km_isi_sek = $request->km_isi_sek;
            $dataBBM->km_akhir = $request->km_akhir;
            $dataBBM->km_ltr = $request->km_ltr;
            $dataBBM->harga = $numericHarga;
            $dataBBM->tot_harga = $numericTotalHarga;
            $dataBBM->ket = $request->ket;
            $dataBBM->tot_km = $request->tot_km;

            if ($filePath) {
                $dataBBM->file = $filePath;
            }

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
            $bbm = BBM::orderBy('tanggal', 'asc')->orderBy('id_kendaraan', 'asc')->get();
            return Excel::download(new BBMExport($mode, $bbm, $rangeDate), 'Report BBM.xlsx');

        } else {
            $bbm = BBM::where('tanggal', '>=', $start_date)
                ->where('tanggal', '<=', $end_date)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id_kendaraan', 'asc')
                ->get();

            $fileName = 'Report BBM ' . $rangeDate . '.xlsx';
            return Excel::download(new BBMExport($mode, $bbm, $rangeDate), $fileName);
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

        BBM::whereIn('id_bbm', $validatedIds)->update([
            'status' => 'pending'
        ]);
        return redirect('bbm');
    }

    public function process(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        BBM::whereIn('id_bbm', $validatedIds)->update([
            'status' => 'processing'
        ]);
        return redirect('bbm');
    }

    public function paid(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        BBM::whereIn('id_bbm', $validatedIds)->update([
            'status' => 'paid'
        ]);
        return redirect('bbm');
    }
}
