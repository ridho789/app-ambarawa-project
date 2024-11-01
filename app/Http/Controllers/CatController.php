<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanAMB;
use App\Models\Kendaraan;
use App\Models\Satuan;
use App\Models\Toko;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanNopolExport;
use Carbon\Carbon;
use DateTime;

class CatController extends Controller
{
    public function index() {
        $cat = TagihanAMB::whereIn('keterangan', ['tagihan cat', 'tagihan cat online'])
            ->where(function ($query) {
                $query->whereNull('noform')
                    ->orWhereHas('permintaanBarang', function ($query) {
                        $query->whereColumn('tbl_tagihan_amb.noform', 'tbl_permintaan_barang.noform')
                                ->where('status', 'approved');
                    });
            })
            ->orderBy('lokasi')
            ->orderBy('tgl_order')
            ->orderBy('nama')
            ->get();

        $catOnline = TagihanAMB::where('keterangan', 'tagihan cat online')
        ->where(function ($query) {
            $query->whereNull('noform')
                ->orWhereHas('permintaanBarang', function ($query) {
                    $query->whereColumn('tbl_tagihan_amb.noform', 'tbl_permintaan_barang.noform')
                            ->where('status', 'approved');
                });
        })->get();
        $catOffline = TagihanAMB::where('keterangan', 'tagihan cat')
        ->where(function ($query) {
            $query->whereNull('noform')
                ->orWhereHas('permintaanBarang', function ($query) {
                    $query->whereColumn('tbl_tagihan_amb.noform', 'tbl_permintaan_barang.noform')
                            ->where('status', 'approved');
                });
        })->get();
        $kendaraan = Kendaraan::all();
        $nopolKendaraan = Kendaraan::pluck('nopol', 'id_kendaraan');
        $merkKendaraan = Kendaraan::pluck('merk', 'id_kendaraan');
        $satuan = Satuan::all();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        $toko = Toko::all();
        $namaToko = Toko::pluck('nama', 'id_toko');
        return view('contents.cat_amb', compact('cat', 'catOnline', 'catOffline', 'kendaraan', 'nopolKendaraan', 'merkKendaraan', 'satuan', 'namaSatuan', 'toko', 'namaToko'));
    }

    public function store(Request $request) {
        $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $masa_pakai = $request->masa . ' ' . $request->waktu;

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
            $filePath = $file->storeAs('Cat', $fileName, 'public');
        }

        $dataToko = Toko::where('id_toko', $request->toko)->first();
        $namaToko = 'null';
        if ($dataToko) {
            $namaToko = $dataToko->nama;
        }

        if ($request->metode_pembelian == 'offline') {
            $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);

            $dataCatAMB = [
                'keterangan' => 'tagihan cat',
                'lokasi' => $request->lokasi,
                'id_kendaraan' => $request->kendaraan,
                'pemesan' => $request->pemesan,
                'tgl_order' => $request->tgl_order,
                'tgl_invoice' => $request->tgl_invoice,
                'no_inventaris' => $request->no_inventaris,
                'nama' => $request->nama,
                'kategori' => $request->kategori,
                'dipakai_untuk' => $request->dipakai_untuk,
                'masa_pakai' => $masa_pakai,
                'jml' => $request->jml,
                'id_satuan' => $request->unit,
                'harga' => $numericHarga,
                'harga_online' => null,
                'ongkir' => null,
                'diskon_ongkir' => null,
                'asuransi' => null,
                'b_proteksi' => null,
                'b_jasa_aplikasi' => null,
                'total' => $numericTotal,
                'id_toko' => $request->toko,
                'file' => $filePath
            ];
    
            $exitingCat = TagihanAMB::where('keterangan', 'tagihan cat')
                ->where('lokasi', $request->lokasi)
                ->where('id_kendaraan', $request->kendaraan)
                ->where('pemesan', $request->pemesan)
                ->where('tgl_order', $request->tgl_order)
                ->where('tgl_invoice', $request->tgl_invoice)
                ->where('no_inventaris', $request->no_inventaris)
                ->where('nama', $request->nama)
                ->where('kategori', $request->kategori)
                ->where('dipakai_untuk', $request->dipakai_untuk)
                ->where('masa_pakai', $masa_pakai)
                ->where('jml', $request->jml)
                ->where('id_satuan', $request->unit)
                ->where('harga', $numericHarga)
                ->where('total', $numericTotal)
                ->where('id_toko', $request->toko)
                ->first();
    
            if ($exitingCat) {
                $logErrors = 'Keterangan: Tagihan Cat (Offline) - Lokasi: ' . $request->lokasi . 
                    ' - Pemesan: ' . $request->pemesan . 
                    ' - Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . 
                    ' - Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . 
                    ' - Nama: ' . $request->nama . 
                    ' - Kategori: ' . $request->kategori . 
                    ' - Dipakai untuk: ' . $request->dipakai_untuk . 
                    ' - Harga: ' . $request->harga . 
                    ' - Toko: ' . $namaToko . ', data tersebut sudah ada di sistem';
            
                return redirect('cat')->with('logErrors', $logErrors);
    
            } else {
                TagihanAMB::create($dataCatAMB);
                return redirect('cat');
            }
        }

        if ($request->metode_pembelian == 'online') {
            $numericHargaOnline = preg_replace("/[^0-9]/", "", explode(",", $request->harga_online)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericDiskonOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->diskon_ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_jasa_aplikasi)[0]);

            $dataCatAMB = [
                'keterangan' => 'tagihan cat online',
                'lokasi' => $request->lokasi,
                'id_kendaraan' => $request->kendaraan,
                'pemesan' => $request->pemesan,
                'tgl_order' => $request->tgl_order,
                'tgl_invoice' => $request->tgl_invoice,
                'no_inventaris' => $request->no_inventaris,
                'nama' => $request->nama,
                'kategori' => $request->kategori,
                'dipakai_untuk' => $request->dipakai_untuk,
                'masa_pakai' => $masa_pakai,
                'jml' => $request->jml_onl,
                'id_satuan' => $request->unit_onl,
                'harga' => null,
                'harga_online' => $numericHargaOnline,
                'ongkir' => $numericOngkir,
                'diskon_ongkir' => $numericDiskonOngkir,
                'asuransi' => $numericAsuransi,
                'b_proteksi' => $numericProteksi,
                'b_jasa_aplikasi' => $numericAplikasi,
                'total' => $numericTotal,
                'id_toko' => $request->toko,
                'via' => 'online',
                'file' => $filePath
            ];
    
            $exitingCat = TagihanAMB::where('keterangan', 'tagihan cat online')
                ->where('lokasi', $request->lokasi)
                ->where('id_kendaraan', $request->kendaraan)
                ->where('pemesan', $request->pemesan)
                ->where('tgl_order', $request->tgl_order)
                ->where('tgl_invoice', $request->tgl_invoice)
                ->where('no_inventaris', $request->no_inventaris)
                ->where('nama', $request->nama)
                ->where('kategori', $request->kategori)
                ->where('dipakai_untuk', $request->dipakai_untuk)
                ->where('masa_pakai', $masa_pakai)
                ->where('jml', $request->jml_onl)
                ->where('id_satuan', $request->unit_onl)
                ->where('harga_online', $numericHargaOnline)
                ->where('ongkir', $numericOngkir)
                ->where('diskon_ongkir', $numericDiskonOngkir)
                ->where('asuransi', $numericAsuransi)
                ->where('b_proteksi', $numericProteksi)
                ->where('b_jasa_aplikasi', $numericAplikasi)
                ->where('total', $numericTotal)
                ->where('id_toko', $request->toko)
                ->first();
    
            if ($exitingCat) {
                $logErrors = 'Keterangan: Tagihan Cat (Online) - Lokasi: ' . $request->lokasi . 
                    ' - Pemesan: ' . $request->pemesan . 
                    ' - Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . 
                    ' - Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . 
                    ' - Nama: ' . $request->nama . 
                    ' - Kategori: ' . $request->kategori . 
                    ' - Dipakai untuk: ' . $request->dipakai_untuk . 
                    ' - Harga: ' . $request->harga_online . 
                    ' - Toko: ' . $namaToko . ', data tersebut sudah ada di sistem';
            
                return redirect('cat')->with('logErrors', $logErrors);
    
            } else {
                TagihanAMB::create($dataCatAMB);
                return redirect('cat');
            }
        }

        $dataCatAMB = [
            'keterangan' => 'tagihan cat',
            'lokasi' => $request->lokasi,
            'id_kendaraan' => $request->kendaraan,
            'pemesan' => $request->pemesan,
            'tgl_order' => $request->tgl_order,
            'tgl_invoice' => $request->tgl_invoice,
            'no_inventaris' => $request->no_inventaris,
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'dipakai_untuk' => $request->dipakai_untuk,
            'masa_pakai' => $masa_pakai,
            'jml' => null,
            'id_satuan' => null,
            'harga' => null,
            'harga_online' => null,
            'ongkir' => null,
            'diskon_ongkir' => null,
            'asuransi' => null,
            'b_proteksi' => null,
            'b_jasa_aplikasi' => null,
            'total' => $numericTotal,
            'id_toko' => $request->toko,
            'file' => $filePath
        ];

        $exitingCat = TagihanAMB::where('keterangan', 'tagihan cat')
            ->where('lokasi', $request->lokasi)
            ->where('id_kendaraan', $request->kendaraan)
            ->where('pemesan', $request->pemesan)
            ->where('tgl_order', $request->tgl_order)
            ->where('tgl_invoice', $request->tgl_invoice)
            ->where('no_inventaris', $request->no_inventaris)
            ->where('nama', $request->nama)
            ->where('kategori', $request->kategori)
            ->where('dipakai_untuk', $request->dipakai_untuk)
            ->where('masa_pakai', $request->masa_pakai)
            ->where('total', $numericTotal)
            ->where('id_toko', $request->toko)
            ->first();

        if ($exitingCat) {
            $logErrors = 'Keterangan: Tagihan Cat (Offline) - Lokasi: ' . $request->lokasi . 
                ' - Pemesan: ' . $request->pemesan . 
                ' - Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . 
                ' - Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . 
                ' - Nama: ' . $request->nama . 
                ' - Kategori: ' . $request->kategori . 
                ' - Dipakai untuk: ' . $request->dipakai_untuk . 
                ' - Toko: ' . $namaToko . ', data tersebut sudah ada di sistem';
        
            return redirect('cat')->with('logErrors', $logErrors);

        } else {
            TagihanAMB::create($dataCatAMB);
            return redirect('cat');
        }
    }

    public function update(Request $request) {
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $tagihanCat = TagihanAMB::find($request->id_tagihan_amb);
        $masa_pakai = $request->masa . ' ' . $request->waktu;

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
            $filePath = $file->storeAs('Cat', $fileName, 'public');
        }

        if ($request->metode_pembelian == 'offline') {
            $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);

            if ($tagihanCat) {
                $tagihanCat->keterangan = 'tagihan cat';
                $tagihanCat->lokasi = $request->lokasi;
                $tagihanCat->id_kendaraan = $request->kendaraan;
                $tagihanCat->pemesan = $request->pemesan;
                $tagihanCat->tgl_order = $request->tgl_order;
                $tagihanCat->tgl_invoice = $request->tgl_invoice;
                $tagihanCat->no_inventaris = $request->no_inventaris;
                $tagihanCat->nama = $request->nama;
                $tagihanCat->kategori = $request->kategori;
                $tagihanCat->dipakai_untuk = $request->dipakai_untuk;
                $tagihanCat->masa_pakai = $masa_pakai;
                $tagihanCat->jml = $request->jml;
                $tagihanCat->id_satuan = $request->unit;
                $tagihanCat->harga = $numericHarga;
                $tagihanCat->harga_online = null;
                $tagihanCat->diskon_ongkir = null;
                $tagihanCat->ongkir = null;
                $tagihanCat->asuransi = null;
                $tagihanCat->b_proteksi = null;
                $tagihanCat->b_jasa_aplikasi = null;
                $tagihanCat->total = $numericTotal;
                $tagihanCat->id_toko = $request->toko;

                if ($filePath) {
                    $tagihanCat->file = $filePath;
                }
    
                $tagihanCat->save();
                return redirect('cat?page=' . $request->page)->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($request->metode_pembelian == 'online') {
            $numericHargaOnline = preg_replace("/[^0-9]/", "", explode(",", $request->harga_online)[0]);
            $numericDiskonOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->diskon_ongkir)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_jasa_aplikasi)[0]);

            if ($tagihanCat) {
                $tagihanCat->keterangan = 'tagihan cat online';
                $tagihanCat->lokasi = $request->lokasi;
                $tagihanCat->id_kendaraan = $request->kendaraan;
                $tagihanCat->pemesan = $request->pemesan;
                $tagihanCat->tgl_order = $request->tgl_order;
                $tagihanCat->tgl_invoice = $request->tgl_invoice;
                $tagihanCat->no_inventaris = $request->no_inventaris;
                $tagihanCat->nama = $request->nama;
                $tagihanCat->kategori = $request->kategori;
                $tagihanCat->dipakai_untuk = $request->dipakai_untuk;
                $tagihanCat->masa_pakai = $masa_pakai;
                $tagihanCat->jml = $request->jml_onl;
                $tagihanCat->id_satuan = $request->unit_onl;
                $tagihanCat->harga = null;
                $tagihanCat->harga_online = $numericHargaOnline;
                $tagihanCat->diskon_ongkir = $numericDiskonOngkir;
                $tagihanCat->ongkir = $numericOngkir;
                $tagihanCat->asuransi = $numericAsuransi;
                $tagihanCat->b_proteksi = $numericProteksi;
                $tagihanCat->b_jasa_aplikasi = $numericAplikasi;
                $tagihanCat->total = $numericTotal;
                $tagihanCat->id_toko = $request->toko;
                $tagihanCat->via = 'online';

                if ($filePath) {
                    $tagihanCat->file = $filePath;
                }
    
                $tagihanCat->save();
                return redirect('cat')->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($tagihanCat) {
            $tagihanCat->keterangan = 'tagihan cat';
            $tagihanCat->lokasi = $request->lokasi;
            $tagihanCat->id_kendaraan = $request->kendaraan;
            $tagihanCat->pemesan = $request->pemesan;
            $tagihanCat->tgl_order = $request->tgl_order;
            $tagihanCat->tgl_invoice = $request->tgl_invoice;
            $tagihanCat->no_inventaris = $request->no_inventaris;
            $tagihanCat->nama = $request->nama;
            $tagihanCat->kategori = $request->kategori;
            $tagihanCat->dipakai_untuk = $request->dipakai_untuk;
            $tagihanCat->masa_pakai = $masa_pakai;
            $tagihanCat->jml = null;
            $tagihanCat->id_satuan = null;
            $tagihanCat->harga = null;
            $tagihanCat->harga_online = null;
            $tagihanCat->diskon_ongkir = null;
            $tagihanCat->ongkir = null;
            $tagihanCat->asuransi = null;
            $tagihanCat->b_proteksi = null;
            $tagihanCat->b_jasa_aplikasi = null;
            $tagihanCat->total = $numericTotal;
            $tagihanCat->id_toko = $request->toko;

            if ($filePath) {
                $tagihanCat->file = $filePath;
            }

            $tagihanCat->save();
            return redirect('cat')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('cat');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->delete();
        return redirect('cat');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $metode_pembelian = 'offline';
        $infoTagihan = 'Cat';
        
        if ($request->metode_pembelian) {
            $metode_pembelian = $request->metode_pembelian;
        }

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
    
        $hargaColumn = $metode_pembelian == 'online' ? 'harga_online' : null;
        $query = TagihanAMB::whereIn('keterangan', ['tagihan cat', 'tagihan cat online'])
        ->where(function ($query) {
            $query->whereNull('noform')
                ->orWhereHas('permintaanBarang', function ($query) {
                    $query->whereColumn('tbl_tagihan_amb.noform', 'tbl_permintaan_barang.noform')
                            ->where('status', 'approved');
                });
        })
        ->when($hargaColumn, function ($query, $hargaColumn) {
            return $query->where('via', 'online')
                        ->where('keterangan', 'tagihan cat online');
        }, function ($query) {
            return $query->where('via', 'offline')
                        ->where('keterangan', 'tagihan cat');
        });
        
        if ($mode != 'all_data') {
            $query->where('tgl_order', '>=', $start_date)
                  ->where('tgl_order', '<=', $end_date);
        }
        
        $tagihan = $query->orderBy('tgl_order', 'asc')->orderBy('lokasi', 'asc')->orderBy('nama', 'asc')->get();
    
        // Tentukan nama file
        $fileName = $mode == 'all_data' 
            ? ($metode_pembelian == 'online' ? 'Report Cat Online.xlsx' : 'Report Cat.xlsx') 
            : ($metode_pembelian == 'online' 
                ? 'Report Cat Online ' . $rangeDate . '.xlsx' 
                : 'Report Cat ' . $rangeDate . '.xlsx');
    
        return Excel::download(new TagihanNopolExport($mode, $tagihan, $infoTagihan, $metode_pembelian, $rangeDate), $fileName);
    }

    // Update status
    public function pending(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->update([
            'status' => 'pending'
        ]);
        return redirect('cat');
    }

    public function process(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->update([
            'status' => 'processing'
        ]);
        return redirect('cat');
    }

    public function paid(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->update([
            'status' => 'paid'
        ]);
        return redirect('cat');
    }
}
