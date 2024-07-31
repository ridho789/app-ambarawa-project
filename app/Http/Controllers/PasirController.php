<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembangunan;
use App\Models\Proyek;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PembangunanExport;

class PasirController extends Controller
{
    public function index() {
        $pasir = Pembangunan::where('ket', 'pengeluaran pasir')->orderBy('tanggal')->orderBy('nama')->get();
        $periodes = Pembangunan::where('ket', 'pengeluaran pasir')
            ->select(Pembangunan::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        $proyek = Proyek::all();
        $namaProyek = Proyek::pluck('nama', 'id_proyek');
        return view('contents.pembangunan.kontruksi.pasir', compact('pasir', 'proyek', 'namaProyek', 'periodes'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);

        $dataUrPasir = [
            'ket' => 'pengeluaran pasir',
            'id_proyek' => $request->proyek,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'ukuran' => $request->ukuran,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'harga' => $numericHarga,
            'tot_harga' => $numericTotal
        ];

        $dataProyek = Proyek::where('id_proyek', $request->proyek)->first();
        $namaProyek = 'null';
        if ($dataProyek) {
            $namaProyek = $dataProyek->nama;
        } 

        $exitingPasir = Pembangunan::where('tanggal', $request->tanggal)->where('nama', $request->nama)->where('ukuran', $request->ukuran)->where('deskripsi', $request->deskripsi)
            ->where('jumlah', $request->jumlah)->where('satuan', $request->satuan)->where('harga', $numericHarga)->where('tot_harga', $numericTotal)->where('id_proyek', $request->proyek)
            ->first();

        if ($exitingPasir) {
            $logErrors = 'Proyek: ' . $namaProyek . ' - ' . 'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 'Nama (Barang): ' . $request->nama . ' - ' . 
            'Jumlah: ' . $request->jumlah . ' - ' . 'Satuan: ' . $request->satuan . ' - ' . 'Harga: ' . $request->harga . ' - ' . 'Total Harga: ' . $request->total . 
            ', data tersebut sudah ada di sistem';

            return redirect('pasir')->with('logErrors', $logErrors);

        } else {
            Pembangunan::create($dataUrPasir);
            return redirect('pasir');
        }
    }

    public function update(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        
        $tagihanPasir = Pembangunan::find($request->id_pasir);
        if ($tagihanPasir) {
            $tagihanPasir->id_proyek = $request->proyek;
            $tagihanPasir->tanggal = $request->tanggal;
            $tagihanPasir->nama = $request->nama;
            $tagihanPasir->ukuran = $request->ukuran;
            $tagihanPasir->deskripsi = $request->deskripsi;
            $tagihanPasir->jumlah = $request->jumlah;
            $tagihanPasir->satuan = $request->satuan;
            $tagihanPasir->harga = $numericHarga;
            $tagihanPasir->tot_harga = $numericTotal;

            $tagihanPasir->save();
            return redirect('pasir')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('pasir');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Pembangunan::whereIn('id_pembangunan', $validatedIds)->delete();
        return redirect('pasir');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $periode = $request->periode;
        $nama = 'Pasir';

        if ($mode == 'all_data') {
            $pasir = Pembangunan::where('ket', 'pengeluaran pasir')->orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
            return Excel::download(new PembangunanExport($mode, $pasir, $nama), 'Report Pasir.xlsx');

        } else {
            $pasir = Pembangunan::where('ket', 'pengeluaran pasir')->whereYear('tanggal', '=', substr($periode, 0, 4))
                ->whereMonth('tanggal', '=', substr($periode, 5, 2))
                ->orderBy('tanggal', 'asc')
                ->orderBy('nama', 'asc')
                ->get();

            $fileName = 'Report Pasir ' . \Carbon\Carbon::parse($periode)->format('M-Y') . '.xlsx';
            return Excel::download(new PembangunanExport($mode, $pasir, $nama), $fileName);
        }
    }
}
