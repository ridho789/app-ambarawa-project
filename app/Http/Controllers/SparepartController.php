<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanAMB;
use App\Models\Kendaraan;
use App\Models\Satuan;
use App\Models\Toko;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SparepartExport;
use Carbon\Carbon;
use DateTime;

class SparepartController extends Controller
{
    public function index() {
        $sparepartamb = TagihanAMB::whereIn('keterangan', ['tagihan sparepart', 'tagihan sparepart online'])
            ->where(function ($query) {
                $query->whereNull('noform')
                    ->orWhereHas('permintaanBarang', function ($query) {
                        $query->whereColumn('tbl_tagihan_amb.noform', 'tbl_permintaan_barang.noform')
                                ->where('status', 'approved');
                    });
            })
            ->orderBy('lokasi')
            ->orderBy('id_kendaraan')
            ->orderBy('nama')
            ->get();

        $sparepartOnline = TagihanAMB::where('keterangan', 'tagihan sparepart online')
        ->where(function ($query) {
            $query->whereNull('noform')
                ->orWhereHas('permintaanBarang', function ($query) {
                    $query->whereColumn('tbl_tagihan_amb.noform', 'tbl_permintaan_barang.noform')
                            ->where('status', 'approved');
                });
        })->get();
        $sparepartOffline = TagihanAMB::where('keterangan', 'tagihan sparepart')
        ->where(function ($query) {
            $query->whereNull('noform')
                ->orWhereHas('permintaanBarang', function ($query) {
                    $query->whereColumn('tbl_tagihan_amb.noform', 'tbl_permintaan_barang.noform')
                            ->where('status', 'approved');
                });
        })->get();
        $kendaraan = Kendaraan::orderBy('nopol')->get();
        $nopolKendaraan = Kendaraan::pluck('nopol', 'id_kendaraan');
        $merkKendaraan = Kendaraan::pluck('merk', 'id_kendaraan');
        $satuan = Satuan::orderBy('nama')->get();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        $toko = Toko::orderBy('nama')->get();
        $namaToko = Toko::pluck('nama', 'id_toko');
        $periodes = TagihanAMB::whereIn('keterangan', ['tagihan sparepart', 'tagihan sparepart online'])
            ->select(TagihanAMB::raw('DATE_FORMAT(tgl_order, "%Y-%m") as periode'))
            ->distinct()
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode');
        $sparepartambgroup = TagihanAMB::whereIn('keterangan', ['tagihan sparepart', 'tagihan sparepart online'])
            ->selectRaw('id_toko, YEAR(tgl_order) as year, MONTHNAME(tgl_order) as month_name, SUM(total) as total_sum')
            ->groupBy('id_toko', 'year', 'month_name')
            ->havingRaw('SUM(total) > 0')
            ->orderBy('id_toko')
            ->orderBy('year')
            ->orderByRaw('MONTH(tgl_order)')
            ->get();

        return view('contents.sparepart_amb', compact('sparepartamb', 'sparepartOnline', 'sparepartOffline', 'kendaraan', 'nopolKendaraan', 
        'merkKendaraan', 'periodes', 'sparepartambgroup', 'satuan', 'namaSatuan', 'toko', 'namaToko'));
    }

    public function store(Request $request) {
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
            $filePath = $file->storeAs('Sparepart', $fileName, 'public');
        }

        $dataToko = Toko::where('id_toko', $request->toko)->first();
        $namaToko = 'null';
        if ($dataToko) {
            $namaToko = $dataToko->nama;
        }
        
        if ($request->metode_pembelian == 'offline') {
            $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);

            $dataSparepartAMB = [
                'keterangan' => 'tagihan sparepart',
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
    
            $exitingSparepart = TagihanAMB::where('keterangan', 'tagihan sparepart')
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
    
            if ($exitingSparepart) {
                $logErrors = 'Keterangan: Tagihan Sparepart (Offline) - Lokasi: ' . $request->lokasi . 
                    ' - Pemesan: ' . $request->pemesan . 
                    ' - Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . 
                    ' - Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . 
                    ' - Nama: ' . $request->nama . 
                    ' - Kategori: ' . $request->kategori . 
                    ' - Dipakai untuk: ' . $request->dipakai_untuk . 
                    ' - Harga: ' . $request->harga . 
                    ' - Toko: ' . $namaToko . 
                    ', data tersebut sudah ada di sistem';
            
                return redirect('sparepartamb')->with('logErrors', $logErrors);
    
            } else {
                TagihanAMB::create($dataSparepartAMB);
                return redirect('sparepartamb');
            }

        }

        if ($request->metode_pembelian == 'online') {
            $numericHargaOnline = preg_replace("/[^0-9]/", "", explode(",", $request->harga_online)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericDiskonOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->diskon_ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_jasa_aplikasi)[0]);

            $dataSparepartAMB = [
                'keterangan' => 'tagihan sparepart online',
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
    
            $exitingSparepart = TagihanAMB::where('keterangan', 'tagihan sparepart online')
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
            
            if ($exitingSparepart) {
                $logErrors = 'Keterangan: Tagihan Sparepart (Online) - Lokasi: ' . $request->lokasi . 
                    ' - Pemesan: ' . $request->pemesan . 
                    ' - Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . 
                    ' - Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . 
                    ' - Nama: ' . $request->nama . 
                    ' - Kategori: ' . $request->kategori . 
                    ' - Dipakai untuk: ' . $request->dipakai_untuk . 
                    ' - Harga: ' . $request->harga_online . 
                    ' - Toko: ' . $namaToko . 
                    ', data tersebut sudah ada di sistem';

                return redirect('sparepartamb')->with('logErrors', $logErrors);
    
            } else {
                TagihanAMB::create($dataSparepartAMB);
                return redirect('sparepartamb');
            }

        }

        $dataSparepartAMB = [
            'keterangan' => 'tagihan sparepart',
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

        $exitingSparepart = TagihanAMB::where('keterangan', 'tagihan sparepart')
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
            ->where('total', $numericTotal)
            ->where('id_toko', $request->toko)
            ->first();

        if ($exitingSparepart) {
            $logErrors = 'Keterangan: Tagihan Sparepart - Lokasi: ' . $request->lokasi . 
                ' - Pemesan: ' . $request->pemesan . 
                ' - Tgl. Order: ' . date('d-M-Y', strtotime($request->tgl_order)) . 
                ' - Tgl. Invoice: ' . date('d-M-Y', strtotime($request->tgl_invoice)) . 
                ' - Nama: ' . $request->nama . 
                ' - Kategori: ' . $request->kategori . 
                ' - Dipakai untuk: ' . $request->dipakai_untuk . 
                ' - Toko: ' . $namaToko . 
                ', data tersebut sudah ada di sistem';
        
            return redirect('sparepartamb')->with('logErrors', $logErrors);

        } else {
            TagihanAMB::create($dataSparepartAMB);
            return redirect('sparepartamb');
        }
    }

    public function update(Request $request) {
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $tagihanSparepart = TagihanAMB::find($request->id_tagihan_amb);
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
            $filePath = $file->storeAs('Sparepart', $fileName, 'public');
        }

        if ($request->metode_pembelian == 'offline') {
            $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $request->harga)[0]);

            if ($tagihanSparepart) {
                $tagihanSparepart->keterangan = 'tagihan sparepart';
                $tagihanSparepart->lokasi = $request->lokasi;
                $tagihanSparepart->id_kendaraan = $request->kendaraan;
                $tagihanSparepart->pemesan = $request->pemesan;
                $tagihanSparepart->tgl_order = $request->tgl_order;
                $tagihanSparepart->tgl_invoice = $request->tgl_invoice;
                $tagihanSparepart->no_inventaris = $request->no_inventaris;
                $tagihanSparepart->nama = $request->nama;
                $tagihanSparepart->kategori = $request->kategori;
                $tagihanSparepart->dipakai_untuk = $request->dipakai_untuk;
                $tagihanSparepart->masa_pakai = $masa_pakai;
                $tagihanSparepart->jml = $request->jml;
                $tagihanSparepart->id_satuan = $request->unit;
                $tagihanSparepart->harga = $numericHarga;
                $tagihanSparepart->harga_online = null;
                $tagihanSparepart->diskon_ongkir = null;
                $tagihanSparepart->ongkir = null;
                $tagihanSparepart->asuransi = null;
                $tagihanSparepart->b_proteksi = null;
                $tagihanSparepart->b_jasa_aplikasi = null;
                $tagihanSparepart->total = $numericTotal;
                $tagihanSparepart->id_toko = $request->toko;

                if ($filePath) {
                    $tagihanSparepart->file = $filePath;
                }
    
                $tagihanSparepart->save();
                return redirect('sparepartamb?page=' . $request->page)->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($request->metode_pembelian == 'online') {
            $numericHargaOnline = preg_replace("/[^0-9]/", "", explode(",", $request->harga_online)[0]);
            $numericDiskonOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->diskon_ongkir)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_jasa_aplikasi)[0]);

            if ($tagihanSparepart) {
                $tagihanSparepart->keterangan = 'tagihan sparepart online';
                $tagihanSparepart->lokasi = $request->lokasi;
                $tagihanSparepart->id_kendaraan = $request->kendaraan;
                $tagihanSparepart->pemesan = $request->pemesan;
                $tagihanSparepart->tgl_order = $request->tgl_order;
                $tagihanSparepart->tgl_invoice = $request->tgl_invoice;
                $tagihanSparepart->no_inventaris = $request->no_inventaris;
                $tagihanSparepart->nama = $request->nama;
                $tagihanSparepart->kategori = $request->kategori;
                $tagihanSparepart->dipakai_untuk = $request->dipakai_untuk;
                $tagihanSparepart->masa_pakai = $masa_pakai;
                $tagihanSparepart->jml = $request->jml_onl;
                $tagihanSparepart->id_satuan = $request->unit_onl;
                $tagihanSparepart->harga = null;
                $tagihanSparepart->harga_online = $numericHargaOnline;
                $tagihanSparepart->diskon_ongkir = $numericDiskonOngkir;
                $tagihanSparepart->ongkir = $numericOngkir;
                $tagihanSparepart->asuransi = $numericAsuransi;
                $tagihanSparepart->b_proteksi = $numericProteksi;
                $tagihanSparepart->b_jasa_aplikasi = $numericAplikasi;
                $tagihanSparepart->total = $numericTotal;
                $tagihanSparepart->id_toko = $request->toko;
                $tagihanSparepart->via = 'online';

                if ($filePath) {
                    $tagihanSparepart->file = $filePath;
                }
    
                $tagihanSparepart->save();
                return redirect('sparepartamb?page=' . $request->page)->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($tagihanSparepart) {
            $tagihanSparepart->keterangan = 'tagihan sparepart';
            $tagihanSparepart->lokasi = $request->lokasi;
            $tagihanSparepart->id_kendaraan = $request->kendaraan;
            $tagihanSparepart->pemesan = $request->pemesan;
            $tagihanSparepart->tgl_order = $request->tgl_order;
            $tagihanSparepart->tgl_invoice = $request->tgl_invoice;
            $tagihanSparepart->no_inventaris = $request->no_inventaris;
            $tagihanSparepart->nama = $request->nama;
            $tagihanSparepart->kategori = $request->kategori;
            $tagihanSparepart->dipakai_untuk = $request->dipakai_untuk;
            $tagihanSparepart->masa_pakai = $masa_pakai;
            $tagihanSparepart->jml = null;
            $tagihanSparepart->id_satuan = null;
            $tagihanSparepart->harga = null;
            $tagihanSparepart->harga_online = null;
            $tagihanSparepart->diskon_ongkir = null;
            $tagihanSparepart->ongkir = null;
            $tagihanSparepart->asuransi = null;
            $tagihanSparepart->b_proteksi = null;
            $tagihanSparepart->b_jasa_aplikasi = null;
            $tagihanSparepart->total = $numericTotal;
            $tagihanSparepart->id_toko = $request->toko;

            if ($filePath) {
                $tagihanSparepart->file = $filePath;
            }

            $tagihanSparepart->save();
            return redirect('sparepartamb?page=' . $request->page)->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('sparepartamb');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        TagihanAMB::whereIn('id_tagihan_amb', $validatedIds)->delete();
        return redirect('sparepartamb');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $metode_pembelian = 'offline';
        $infoTagihan = 'Sparepart';
        
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
        $query = TagihanAMB::whereIn('keterangan', ['tagihan sparepart', 'tagihan sparepart online'])
        ->where(function ($query) {
            $query->whereNull('noform')
                ->orWhereHas('permintaanBarang', function ($query) {
                    $query->whereColumn('tbl_tagihan_amb.noform', 'tbl_permintaan_barang.noform')
                            ->where('status', 'approved');
                });
        })
        ->when($hargaColumn, function ($query, $hargaColumn) {
            return $query->where('via', 'online')
                        ->where('keterangan', 'tagihan sparepart online');
        }, function ($query) {
            return $query->where('via', 'offline')
                        ->where('keterangan', 'tagihan sparepart');
        });
        
        if ($mode != 'all_data') {
            $query->where('tgl_order', '>=', $start_date)
                  ->where('tgl_order', '<=', $end_date);
        }
        
        $tagihan = $query->orderBy('tgl_order', 'asc')->orderBy('lokasi', 'asc')->orderBy('nama', 'asc')->get();
    
        // Tentukan nama file
        $fileName = $mode == 'all_data' 
            ? ($metode_pembelian == 'online' ? 'Report Sparepart Online.xlsx' : 'Report Sparepart.xlsx') 
            : ($metode_pembelian == 'online' 
                ? 'Report Sparepart Online ' . $rangeDate . '.xlsx' 
                : 'Report Sparepart ' . $rangeDate . '.xlsx');
    
        return Excel::download(new SparepartExport($mode, $tagihan, $infoTagihan, $metode_pembelian,$rangeDate), $fileName);
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
        return redirect('sparepartamb');
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
        return redirect('sparepartamb');
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
        return redirect('sparepartamb');
    }
}
