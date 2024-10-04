<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operasional;
use App\Models\Barang;
use App\Models\Satuan;
use App\Models\Toko;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OperasionalExport;
use Carbon\Carbon;
use DateTime;

class OperasionalController extends Controller
{
    public function index() {
        $operasional = Operasional::orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
        $opsOnline = Operasional::where('metode_pembelian', 'online')->get();
        $opsOffline = Operasional::where('metode_pembelian', 'offline')->get();
        $barang = Barang::orderBy('nama')->get();
        $satuan = Satuan::orderBy('nama')->get();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        $toko = Toko::orderBy('nama')->get();
        $namaToko = Toko::pluck('nama', 'id_toko');
        return view('contents.operasional', compact('operasional', 'opsOnline', 'opsOffline', 'barang', 'satuan', 'toko', 'namaToko', 'namaSatuan'));
    }

    public function store(Request $request) {
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
            $filePath = $file->storeAs('Ops', $fileName, 'public');
        }

        $dataToko = Toko::where('id_toko', $request->toko)->first();
        $namaToko = 'null';
        if ($dataToko) {
            $namaToko = $dataToko->nama;
        }

        if ($request->metode_pembelian == 'offline') {
            $dataOperasional = [
                'tanggal' => $request->tanggal,
                'uraian' => $request->uraian,
                'deskripsi' => $request->deskripsi,
                'nama' => $request->nama,
                'total' => $numericTotal,
                'id_toko' => $request->toko,
                'metode_pembelian' => $request->metode_pembelian,
                'lokasi' => $request->lokasi,
                'file' => $filePath
            ];

            $exitingOperasional = Operasional::where('tanggal', $request->tanggal)
                ->where('uraian', $request->uraian)
                ->where('deskripsi', $request->deskripsi)
                ->where('nama', $request->nama)
                ->where('total', $numericTotal)
                ->where('id_toko', $request->toko)
                ->where('metode_pembelian', $request->metode_pembelian)
                ->where('lokasi', $request->lokasi)
                ->first();
    
            if ($exitingOperasional) {
                $logErrors = 
                'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . 
                ' - Uraian: ' . $request->uraian . 
                ' - Deskripsi: ' . $request->deskripsi . 
                ' - Nama: ' . $request->nama . 
                ' - Total: ' . $request->total . 
                ' - Lokasi: ' . $request->lokasi . 
                ' - Toko: ' . $namaToko . ', data tersebut sudah ada di sistem';
    
                return redirect('operasional')->with('logErrors', $logErrors);
    
            } else {
                $createdOperasional = Operasional::create($dataOperasional);
                $operasionalId = $createdOperasional->id_operasional;

                // Barang
                foreach ($request->nama_barang as $index => $namaBarang) {
                    $numericHarga = preg_replace("/[^0-9]/", "", $request->harga[$index]);
                    $dataBarang = [
                        'nama' => $namaBarang,
                        'jumlah' => $request->qty[$index],
                        'harga' => $numericHarga,
                        'id_satuan' => $request->unit[$index],
                        'id_relasi' => $operasionalId
                    ];
                
                    Barang::create($dataBarang);
                }

                return redirect('operasional');
            }

        }

        if ($request->metode_pembelian == 'online') {
            $numericDiskon = preg_replace("/[^0-9]/", "", explode(",", $request->diskon)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericMember = preg_replace("/[^0-9]/", "", explode(",", $request->p_member)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_aplikasi)[0]);
            $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
    
            $dataOperasional = [
                'tanggal' => $request->tanggal,
                'uraian' => $request->uraian,
                'deskripsi' => $request->deskripsi,
                'nama' => $request->nama,
                'diskon' => $numericDiskon,
                'ongkir' => $numericOngkir,
                'asuransi' => $numericAsuransi,
                'b_proteksi' => $numericProteksi,
                'p_member' => $numericMember,
                'b_aplikasi' => $numericAplikasi,
                'total' => $numericTotal,
                'id_toko' => $request->toko,
                'metode_pembelian' => $request->metode_pembelian,
                'lokasi' => $request->lokasi,
                'file' => $filePath
            ];
            
            $exitingOperasional = Operasional::where('tanggal', $request->tanggal)
                ->where('uraian', $request->uraian)
                ->where('deskripsi', $request->deskripsi)
                ->where('nama', $request->nama)
                ->where('diskon', $numericDiskon)
                ->where('ongkir', $numericOngkir)
                ->where('asuransi', $numericAsuransi)
                ->where('b_proteksi', $numericProteksi)
                ->where('p_member', $numericMember)
                ->where('b_aplikasi', $numericAplikasi)
                ->where('total', $numericTotal)
                ->where('id_toko', $request->toko)
                ->where('metode_pembelian', $request->metode_pembelian)
                ->where('lokasi', $request->lokasi)
                ->first();
    
            if ($exitingOperasional) {
                $logErrors = 
                'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 
                'Uraian: ' . $request->uraian . ' - ' .
                'Deskripsi: ' . $request->deskripsi . ' - ' .
                'Nama: ' . $request->nama . ' - ' . 
                'Total: ' . $request->total . ' - ' . 
                'Lokasi: ' . $request->lokasi . ' - ' . 
                'Toko: ' . $namaToko . ', data tersebut sudah ada di sistem';
    
                return redirect('operasional')->with('logErrors', $logErrors);
    
            } else {
                $createdOperasional = Operasional::create($dataOperasional);
                $operasionalId = $createdOperasional->id_operasional;

                // Barang
                foreach ($request->nama_barang as $index => $namaBarang) {
                    $numericHarga = preg_replace("/[^0-9]/", "", $request->harga[$index]);
                    $dataBarang = [
                        'nama' => $namaBarang,
                        'jumlah' => $request->qty[$index],
                        'harga' => $numericHarga,
                        'id_satuan' => $request->unit[$index],
                        'id_relasi' => $operasionalId
                    ];
                
                    Barang::create($dataBarang);
                }
                
                return redirect('operasional');
            }
        }

        $dataOperasional = [
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'deskripsi' => $request->deskripsi,
            'nama' => $request->nama,
            'total' => $numericTotal,
            'id_toko' => $request->toko,
            'metode_pembelian' => 'offline',
            'lokasi' => $request->lokasi,
            'file' => $filePath
        ];

        $exitingOperasional = Operasional::where('tanggal', $request->tanggal)
            ->where('uraian', $request->uraian)
            ->where('deskripsi', $request->deskripsi)
            ->where('nama', $request->nama)
            ->where('total', $numericTotal)
            ->where('id_toko', $request->toko)
            ->where('metode_pembelian', $request->metode_pembelian)
            ->where('lokasi', $request->lokasi)
            ->first();

        if ($exitingOperasional) {
            $logErrors = 
            'Tanggal: ' . date('d-M-Y', strtotime($request->tanggal)) . ' - ' . 
            'Uraian: ' . $request->uraian . ' - ' . 
            'Deskripsi: ' . $request->deskripsi . ' - ' . 
            'Nama: ' . $request->nama . ' - ' . 
            'Total: ' . $request->total . ' - ' . 
            'Lokasi: ' . $request->lokasi . ' - ' . 
            'Toko: ' . $namaToko . ', data tersebut sudah ada di sistem';

            return redirect('operasional')->with('logErrors', $logErrors);

        } else {
            Operasional::create($dataOperasional);
            return redirect('operasional');
        }
    }

    public function update(Request $request) {
        $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $request->total)[0]);
        $tagihanOperasional = Operasional::find($request->id_operasional);

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
            $filePath = $file->storeAs('Ops', $fileName, 'public');
        }

        if ($request->metode_pembelian == 'offline') {
            if ($tagihanOperasional) {
                $tagihanOperasional->tanggal = $request->tanggal;
                $tagihanOperasional->uraian = $request->uraian;
                $tagihanOperasional->deskripsi = $request->deskripsi;
                $tagihanOperasional->nama = $request->nama;
                $tagihanOperasional->diskon = null;
                $tagihanOperasional->ongkir = null;
                $tagihanOperasional->asuransi = null;
                $tagihanOperasional->b_proteksi = null;
                $tagihanOperasional->p_member = null;
                $tagihanOperasional->b_aplikasi = null;
                $tagihanOperasional->total = $numericTotal;
                $tagihanOperasional->id_toko = $request->toko;
                $tagihanOperasional->lokasi = $request->lokasi;
                $tagihanOperasional->metode_pembelian = $request->metode_pembelian;

                if ($filePath) {
                    $tagihanOperasional->file = $filePath;
                }
    
                $tagihanOperasional->save();

                // Update barang
                foreach ($request->id_barang as $index => $barang) {
                    $dataBarang = Barang::find($barang);
                    $numericHarga = preg_replace("/[^0-9]/", "", $request->harga[$index]);
                    $data = [
                        'nama' => $request->nama_barang[$index],
                        'jumlah' => $request->qty[$index],
                        'harga' => $numericHarga,
                        'id_satuan' => $request->unit[$index],
                    ];
                
                    if ($dataBarang) {
                        $dataBarang->update($data);
                    }
                }

                return redirect('operasional')->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($request->metode_pembelian == 'online') {
            $numericDiskon = preg_replace("/[^0-9]/", "", explode(",", $request->diskon)[0]);
            $numericOngkir = preg_replace("/[^0-9]/", "", explode(",", $request->ongkir)[0]);
            $numericAsuransi = preg_replace("/[^0-9]/", "", explode(",", $request->asuransi)[0]);
            $numericProteksi = preg_replace("/[^0-9]/", "", explode(",", $request->b_proteksi)[0]);
            $numericMember = preg_replace("/[^0-9]/", "", explode(",", $request->p_member)[0]);
            $numericAplikasi = preg_replace("/[^0-9]/", "", explode(",", $request->b_aplikasi)[0]);

            if ($tagihanOperasional) {
                $tagihanOperasional->tanggal = $request->tanggal;
                $tagihanOperasional->uraian = $request->uraian;
                $tagihanOperasional->deskripsi = $request->deskripsi;
                $tagihanOperasional->nama = $request->nama;
                $tagihanOperasional->diskon = $numericDiskon;
                $tagihanOperasional->ongkir = $numericOngkir;
                $tagihanOperasional->asuransi = $numericAsuransi;
                $tagihanOperasional->b_proteksi = $numericProteksi;
                $tagihanOperasional->p_member = $numericMember;
                $tagihanOperasional->b_aplikasi = $numericAplikasi;
                $tagihanOperasional->total = $numericTotal;
                $tagihanOperasional->id_toko = $request->toko;
                $tagihanOperasional->lokasi = $request->lokasi;
                $tagihanOperasional->metode_pembelian = $request->metode_pembelian;

                if ($filePath) {
                    $tagihanOperasional->file = $filePath;
                }
    
                $tagihanOperasional->save();

                // Update barang
                foreach ($request->id_barang as $index => $barang) {
                    $dataBarang = Barang::find($barang);
                    $numericHarga = preg_replace("/[^0-9]/", "", $request->harga[$index]);
                    $data = [
                        'nama' => $request->nama_barang[$index],
                        'jumlah' => $request->qty[$index],
                        'harga' => $numericHarga,
                        'id_satuan' => $request->unit[$index],
                    ];
                
                    if ($dataBarang) {
                        $dataBarang->update($data);
                    }
                }

                return redirect('operasional')->with('success', 'Data berhasil diperbaharui!');
            }
        }

        if ($tagihanOperasional) {
            $tagihanOperasional->tanggal = $request->tanggal;
            $tagihanOperasional->uraian = $request->uraian;
            $tagihanOperasional->deskripsi = $request->deskripsi;
            $tagihanOperasional->nama = $request->nama;
            $tagihanOperasional->diskon = null;
            $tagihanOperasional->ongkir = null;
            $tagihanOperasional->asuransi = null;
            $tagihanOperasional->b_proteksi = null;
            $tagihanOperasional->p_member = null;
            $tagihanOperasional->b_aplikasi = null;
            $tagihanOperasional->total = $numericTotal;
            $tagihanOperasional->id_toko = $request->toko;
            $tagihanOperasional->lokasi = $request->lokasi;
            $tagihanOperasional->metode_pembelian = 'offline';

            if ($filePath) {
                $tagihanOperasional->file = $filePath;
            }

            $tagihanOperasional->save();
            return redirect('operasional')->with('success', 'Data berhasil diperbaharui!');
        }

        return redirect('operasional');
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Operasional::whereIn('id_operasional', $validatedIds)->delete();
        Barang::whereIn('id_relasi', $validatedIds)->delete();
        return redirect('operasional');
    }

    public function export(Request $request) {
        $mode = $request->metode_export;
        $metode_pembelian = $request->metode_pembelian;
        $infoTagihan = 'Ops';

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
    
        $query = Operasional::when($metode_pembelian == 'online_dan_offline', function ($query) {
            return $query->where(function ($query) {
                $query->where('metode_pembelian', 'online')
                      ->orWhere('metode_pembelian', 'offline');
            });
        }, function ($query) use ($metode_pembelian) {
            if ($metode_pembelian == 'online') {
                return $query->where('metode_pembelian', 'online');
            } elseif ($metode_pembelian == 'offline') {
                return $query->where('metode_pembelian', 'offline');
            }
        
            return $query;
        });
        
        if ($mode != 'all_data') {
            $query->where('tanggal', '>=', $start_date)
                  ->where('tanggal', '<=', $end_date);
        }
        
        $tagihan = $query->orderBy('tanggal', 'asc')->orderBy('nama', 'asc')->get();
    
        // Tentukan nama file
        $fileName = $mode == 'all_data'
        ? ($metode_pembelian == 'online' 
            ? 'Report Ops Online.xlsx' 
            : ($metode_pembelian == 'offline' 
                ? 'Report Ops Offline.xlsx'
                : ($metode_pembelian == 'online_dan_offline'
                    ? 'Report Ops Online dan Offline.xlsx'
                    : 'Report Ops.xlsx')))
        : ($metode_pembelian == 'online'
            ? 'Report Ops Online ' . $rangeDate . '.xlsx'
            : ($metode_pembelian == 'offline'
                ? 'Report Ops Offline ' . $rangeDate . '.xlsx'
                : ($metode_pembelian == 'online_dan_offline'
                    ? 'Report Ops Online dan Offline ' . $rangeDate . '.xlsx'
                    : 'Report Ops ' . $rangeDate . '.xlsx')));

        return Excel::download(new OperasionalExport($mode, $tagihan, $infoTagihan, $metode_pembelian, $rangeDate), $fileName);
    }
    
    // Update status
    public function pending(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Operasional::whereIn('id_operasional', $validatedIds)->update([
            'status' => 'pending'
        ]);
        return redirect('operasional');
    }

    public function process(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Operasional::whereIn('id_operasional', $validatedIds)->update([
            'status' => 'processing'
        ]);
        return redirect('operasional');
    }

    public function paid(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        Operasional::whereIn('id_operasional', $validatedIds)->update([
            'status' => 'paid'
        ]);
        return redirect('operasional');
    }
}
