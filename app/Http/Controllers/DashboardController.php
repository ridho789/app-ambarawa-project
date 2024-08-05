<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanAMB;
use App\Models\BBM;
use App\Models\Operasional;
use App\Models\Sembako;
use App\Models\Trip;
use App\Models\Pembangunan;

class DashboardController extends Controller
{
    public function index() {
        $ac = TagihanAMB::where('keterangan', 'tagihan ac')->sum('total');
        $bbm = BBM::sum('tot_harga');
        $bubut = TagihanAMB::where('keterangan', 'tagihan bubut')->sum('total');
        $cat = TagihanAMB::whereIn('keterangan', ['tagihan cat', 'tagihan cat online'])->sum('total');
        $ops = Operasional::sum('total');
        $poles = TagihanAMB::where('keterangan', 'tagihan poles kaca mobil')->sum('total');
        $sembako = Sembako::sum('total');
        $sparepart = TagihanAMB::whereIn('keterangan', ['tagihan sparepart', 'tagihan sparepart online'])->sum('total');
        $trip = Trip::sum('total');
        $besi = Pembangunan::where('ket', 'pengeluaran besi')->sum('tot_harga');
        $material = Pembangunan::whereNotNull('id_kategori')->sum('tot_harga');
        $pengurugan = Pembangunan::where('ket', 'pengeluaran urug')->sum('tot_harga');
        return view('dashboard', compact('ac', 'bbm', 'bubut', 'cat', 'ops', 'poles', 'sembako', 'sparepart', 'trip', 'besi', 'material', 'pengurugan'));
    }
}
