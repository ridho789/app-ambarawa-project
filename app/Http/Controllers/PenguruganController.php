<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembangunan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PembangunanExport;

class PenguruganController extends Controller
{
    public function index() {
        $pengurugan = Pembangunan::where('ket', 'pengeluaran urug')->orderBy('tanggal')->orderBy('nama')->get();
        $periodes = Pembangunan::where('ket', 'pengeluaran urug')
            ->select(Pembangunan::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        return view('contents.pembangunan.pengurugan', compact('pengurugan', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataUrug = [
            'ket' => 'pengeluaran urug',
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'ukuran' => $request->ukuran,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'harga' => $numericHarga,
            'tot_harga' => $numericTotal
        ];

        $exitingUrug = Pembangunan::where('tanggal', $request->tanggal)->where('nama', $request->nama)->where('ukuran', $request->ukuran)->where('deskripsi', $request->deskripsi)
            ->where('jumlah', $request->jumlah)->where('satuan', $request->satuan)->where('harga', $numericHarga)->where('tot_harga', $numericTotal)
            ->first();

        if ($exitingUrug) {
            $logErrors = 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 
            'Jumlah: ' . $request->jumlah . ' - ' . 'Satuan: ' . $request->satuan . ' - ' . 'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . 
            ', data tersebut sudah ada di sistem';

            return redirect('pengurugan')->with('logErrors', $logErrors);

        } else {
            Pembangunan::create($dataUrug);
            return redirect('pengurugan');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanUrug = Pembangunan::find($request->id_pengurugan);
        if ($tagihanUrug) {
            $tagihanUrug->tanggal = $request->tanggal;
            $tagihanUrug->nama = $request->nama;
            $tagihanUrug->ukuran = $request->ukuran;
            $tagihanUrug->deskripsi = $request->deskripsi;
            $tagihanUrug->jumlah = $request->jumlah;
            $tagihanUrug->satuan = $request->satuan;
            $tagihanUrug->harga = $numericHarga;
            $tagihanUrug->tot_harga = $numericTotal;

            $tagihanUrug->save();
            return redirect('pengurugan')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('pengurugan');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->delete();
        return redirect('pengurugan');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $periode = $request->periode;
        $nama = 'Pengurugan';

        if ($mode == 'all_data') {
            $pengurugan = Pembangunan::where('ket', 'pengeluaran urug')->orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
            return Excel::download(new PembangunanExport($mode, $pengurugan, $nama), 'Report Pengurugan.xlsx');

        } else {
            $pengurugan = Pembangunan::where('ket', 'pengeluaran urug')->whereYear('tanggal', '=', substr($periode, 0, 4))
                ->whereMonth('tanggal', '=', substr($periode, 5, 2))
                ->orderBy('tanggal', 'asc')
                ->orderBy('nama', 'asc')
                ->get();

            $fileName = 'Report Pengurugan ' . \Carbon\Carbon::parse($periode)->format('M-Y') . '.xlsx';
            return Excel::download(new PembangunanExport($mode, $pengurugan, $nama), $fileName);
        }
    }
}
