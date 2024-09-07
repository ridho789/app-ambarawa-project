<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanBarang;
use App\Models\Pembangunan;
use App\Models\Activity;
use App\Models\Barang;
use App\Models\Satuan;
use App\Models\KategoriMaterial;
use App\Models\TagihanAMB;
use Carbon\Carbon;

class PermintaanBarangController extends Controller
{
    public function index() {
        $permintaan_barang = PermintaanBarang::orderBy('noform', 'asc')->orderBy('tgl_order', 'asc')->get();
        $dataPembangunan = Pembangunan::all();
        $dataTagihanAMB = TagihanAMB:: all();
        $satuan = Satuan::all();
        $namaSatuan = Satuan::pluck('nama', 'id_satuan');
        return view('contents.form.permintaan_barang', compact('permintaan_barang', 'dataPembangunan', 'dataTagihanAMB', 'satuan', 'namaSatuan'));
    }

    public function store(Request $request) {
        $kegunaan = $request->kegunaan;
        $subKategori = null;
        if ($request->kategori && $request->kategori == 'MATERIAL') {
            $kegunaan = $request->row_kegunaan;
            $subKategori = $request->sub_kategori;
        }

        $dataPermintaanBarang = [
            'noform' => $request->noform,
            'tgl_order' => $request->tgl_order,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'kategori' => $request->kategori,
            'sub_kategori' => $subKategori,
            'kegunaan' => $kegunaan,
        ];

        $exitingPermintaanBarang = PermintaanBarang::where('noform', $request->noform)->where('tgl_order', $request->tgl_order)
            ->where('nama', $request->nama)->where('jabatan', $request->jabatan)->where('kategori', $request->kategori)->where('kegunaan', $request->kegunaan)
            ->first();
        
        if ($exitingPermintaanBarang) {
            $logErrors = 'No. Form: ' . $request->noform . ' - ' . 'Tanggal: ' . date('d-M-Y', strtotime($request->tgl_order)) . ' - ' . 'Nama: ' . $request->nama . ' - ' . 
            'Jabatan: ' . $request->jabatan . ' - ' . 'Kategori: ' . $request->kategori . ' - ' . 'Kegunaan: ' . $request->kegunaan . ', data tersebut sudah ada di sistem';

            return redirect('permintaan_barang')->with('logErrors', $logErrors);

        } else {
            $createPermintaanBarang = PermintaanBarang::create($dataPermintaanBarang);
            $IdPermintanBarang = $createPermintaanBarang->id_permintaan_barang;

            // create activity
            $dataActivity = [
                'id_relation' => $IdPermintanBarang,
                'description' => 'No. Form: ' . $request->noform,
                'scope' => 'Permintaan Barang',
                'action' => 'Buat Data Baru',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);

            if ($request->kategori && $request->kategori == 'BESI') {
                foreach ($request->nama_barang as $index => $namaBarang) {
                    $idSatuan = $request->unit[$index] ?? null;
                    $dataBesi = [
                        'ket' => 'pengeluaran besi',
                        'noform' => $request->noform,
                        'id_proyek' => null,
                        'nama' => $namaBarang,
                        'tanggal' => $request->tgl_order,
                        'ukuran' => null,
                        'deskripsi' => $request->ket[$index],
                        'jumlah' => $request->jumlah[$index],
                        'id_satuan' => $idSatuan,
                        'harga' => '0',
                        'tot_harga' => '0',
                        'toko' => '-'
                    ];

                    $createBesi = Pembangunan::create($dataBesi);
                    $idBesi = $createBesi->id_pembangunan;

                    // create activity
                    $dataActivity = [
                        'id_relation' => $idBesi,
                        'description' => 'No. Form: ' . $request->noform,
                        'scope' => 'BESI',
                        'action' => 'Buat Data Baru',
                        'user' => $request->user,
                        'action_time' => Carbon::now()->addHours(7),
                    ];

                    Activity::create($dataActivity);
                }
            }

            if ($request->kategori && $request->kategori == 'MATERIAL') {
                $idKatMaterial = null;
                $katMaterial = KategoriMaterial::where('nama', $request->sub_kategori)->first();

                if(empty($katMaterial)) {
                    $dataKatMaterial = KategoriMaterial::create(['nama' => $request->sub_kategori]);
                    $idKatMaterial = $dataKatMaterial->id_kategori;

                } else {
                    $idKatMaterial = $katMaterial->id_kategori;
                }

                foreach ($request->nama_barang as $index => $namaBarang) {
                    $idSatuan = $request->unit[$index] ?? null;
                    $dataMaterial = [
                        'ket' => '-',
                        'noform' => $request->noform,
                        'id_proyek' => null,
                        'id_kategori' => $idKatMaterial,
                        'nama' => $namaBarang,
                        'tanggal' => $request->tgl_order,
                        'ukuran' => null,
                        'deskripsi' => $request->ket[$index],
                        'jumlah' => $request->jumlah[$index],
                        'id_satuan' => $idSatuan,
                        'harga' => '0',
                        'tot_harga' => '0',
                        'toko' => '-'
                    ];

                    $createMaterial = Pembangunan::create($dataMaterial);
                    $idMaterial = $createMaterial->id_pembangunan;

                    // create activity
                    $dataActivity = [
                        'id_relation' => $idMaterial,
                        'description' => 'No. Form: ' . $request->noform,
                        'scope' => 'Material',
                        'action' => 'Buat Data Baru',
                        'user' => $request->user,
                        'action_time' => Carbon::now()->addHours(7),
                    ];

                    Activity::create($dataActivity);
                }
            }

            if ($request->kategori && $request->kategori == 'CAT') {
                foreach ($request->nama_barang as $index => $namaBarang) {
                    $idSatuan = $request->unit[$index] ?? null;
                    $dataCat = [
                        'keterangan' => 'tagihan cat',
                        'noform' => $request->noform,
                        'lokasi' => '-',
                        'pemesan' => $request->nama,
                        'tgl_order' => $request->tgl_order,
                        'tgl_invoice' => null,
                        'no_inventaris' => '-',
                        'nama' => $namaBarang,
                        'kategori' => '-',
                        'dipakai_untuk' => $request->ket[$index],
                        'masa_pakai' => '-',
                        'jml' => $request->jumlah[$index],
                        'id_satuan' => $idSatuan,
                        'total' => '0',
                        'toko' => '-'
                    ];

                    $createCat = TagihanAMB::create($dataCat);
                    $idCat = $createCat->id_tagihan_amb;

                    // create activity
                    $dataActivity = [
                        'id_relation' => $idCat,
                        'description' => 'No. Form: ' . $request->noform,
                        'scope' => 'Cat',
                        'action' => 'Buat Data Baru',
                        'user' => $request->user,
                        'action_time' => Carbon::now()->addHours(7),
                    ];

                    Activity::create($dataActivity);
                }
            }

            if ($request->kategori && $request->kategori == 'SPAREPART') {
                foreach ($request->nama_barang as $index => $namaBarang) {
                    $idSatuan = $request->unit[$index] ?? null;
                    $dataSparepart = [
                        'keterangan' => 'tagihan sparepart',
                        'noform' => $request->noform,
                        'lokasi' => '-',
                        'pemesan' => $request->nama,
                        'tgl_order' => $request->tgl_order,
                        'tgl_invoice' => null,
                        'no_inventaris' => '-',
                        'nama' => $namaBarang,
                        'kategori' => '-',
                        'dipakai_untuk' => $request->ket[$index],
                        'masa_pakai' => '-',
                        'jml' => $request->jumlah[$index],
                        'id_satuan' => $idSatuan,
                        'total' => '0',
                        'toko' => '-'
                    ];

                    $createSparepart = TagihanAMB::create($dataSparepart);
                    $idSparepart = $createSparepart->id_tagihan_amb;

                    // create activity
                    $dataActivity = [
                        'id_relation' => $idSparepart,
                        'description' => 'No. Form: ' . $request->noform,
                        'scope' => 'Sparepart',
                        'action' => 'Buat Data Baru',
                        'user' => $request->user,
                        'action_time' => Carbon::now()->addHours(7),
                    ];

                    Activity::create($dataActivity);
                }
            }

            return redirect('permintaan_barang');
        }
    }

    public function update(Request $request) {
        $kegunaan = $request->kegunaan;
        if ($request->nama_kategori && $request->nama_kategori == 'MATERIAL') {
            $kegunaan = $request->row_kegunaan;
        }

        $permintaanBarang = PermintaanBarang::find($request->id_permintaan_barang);
        if ($permintaanBarang) {
            $permintaanBarang->tgl_order = $request->tgl_order;
            $permintaanBarang->nama = $request->nama;
            $permintaanBarang->jabatan = $request->jabatan;
            $permintaanBarang->kegunaan = $kegunaan;
            $permintaanBarang->save();

            // create update activity
            $dataActivity = [
                'id_relation' => $permintaanBarang->id_permintaan_barang,
                'description' => 'No. Form: ' . $permintaanBarang->noform,
                'scope' => 'Permintaan Barang',
                'action' => 'Update Data',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];

            Activity::create($dataActivity);
        }

        if ($request->nama_kategori && $request->nama_kategori == 'BESI' || $request->nama_kategori == 'MATERIAL') {
            foreach ($request->id_barang as $index => $idBarang) {
                $dataBarang = Pembangunan::find($idBarang);
                $idSatuan = $request->unit[$index] ?? null;
                
                if ($dataBarang) {
                    $dataBarang->jumlah = $request->jumlah[$index];
                    $dataBarang->id_satuan = $idSatuan;
                    $dataBarang->nama = $request->nama_barang[$index];
                    $dataBarang->deskripsi = $request->ket[$index];
                    $dataBarang->save();
                }

                // create update activity
                $dataActivity = [
                    'id_relation' => $idBarang,
                    'description' => 'No. Form: ' . $dataBarang->noform,
                    'scope' => ucfirst(strtolower($request->nama_kategori)),
                    'action' => 'Update Data',
                    'user' => $request->user,
                    'action_time' => Carbon::now()->addHours(7),
                ];

                Activity::create($dataActivity);
            }

            return redirect('permintaan_barang')->with('success', 'Data berhasil diperbaharui!');
        }

        if ($request->nama_kategori && $request->nama_kategori == 'CAT' || $request->nama_kategori == 'SPAREPART') {
            foreach ($request->id_barang as $index => $idBarang) {
                $dataBarang = TagihanAMB::find($idBarang);
                $idSatuan = $request->unit[$index] ?? null;
                
                if ($dataBarang) {
                    $dataBarang->jml = $request->jumlah[$index];
                    $dataBarang->id_satuan = $idSatuan;
                    $dataBarang->nama = $request->nama_barang[$index];
                    $dataBarang->dipakai_untuk = $request->ket[$index];
                    $dataBarang->save();
                }

                // create update activity
                $dataActivity = [
                    'id_relation' => $idBarang,
                    'description' => 'No. Form: ' . $dataBarang->noform,
                    'scope' => ucfirst(strtolower($request->nama_kategori)),
                    'action' => 'Update Data',
                    'user' => $request->user,
                    'action_time' => Carbon::now()->addHours(7),
                ];

                Activity::create($dataActivity);
            }

            return redirect('permintaan_barang')->with('success', 'Data berhasil diperbaharui!');
        }
    }

    public function delete(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);
        $multiNoform = explode(',', $request->multi_noform);
        $multiKategori = explode(',', $request->multi_kategori);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        foreach ($multiKategori as $kategori) {
            if ($kategori == 'BESI' || $kategori == 'MATERIAL') {
                foreach ($multiNoform as $noform) {
                    $dataActivity = [
                        'description' => 'No. Form: ' . $noform,
                        'scope' => ucfirst(strtolower($kategori)),
                        'action' => 'Delete Data',
                        'user' => $request->user,
                        'action_time' => Carbon::now()->addHours(7),
                    ];
                    Activity::create($dataActivity);

                    Pembangunan::where('noform', $noform)->delete();
                }
            }

            if ($kategori == 'CAT' || $kategori == 'OPERASIONAL' || $kategori == 'SPAREPART') {
                foreach ($multiNoform as $noform) {
                    $dataActivity = [
                        'description' => 'No. Form: ' . $noform,
                        'scope' => ucfirst(strtolower($kategori)),
                        'action' => 'Delete Data',
                        'user' => $request->user,
                        'action_time' => Carbon::now()->addHours(7),
                    ];
                    Activity::create($dataActivity);

                    TagihanAMB::where('noform', $noform)->delete();
                }
            }
        }

        $dataPermintaanBarang = PermintaanBarang::whereIn('id_permintaan_barang', $validatedIds)->get();
        foreach ($dataPermintaanBarang as $permintaanBarang) {
            $dataActivity = [
                'description' => 'No. Form: ' . $permintaanBarang->noform,
                'scope' => 'Permintaan Barang',
                'action' => 'Delete Data',
                'user' => $request->user,
                'action_time' => Carbon::now()->addHours(7),
            ];
            Activity::create($dataActivity);
        }

        PermintaanBarang::whereIn('id_permintaan_barang', $validatedIds)->delete();
        return redirect('permintaan_barang');
    }

    // Update status
    public function pending(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        PermintaanBarang::whereIn('id_permintaan_barang', $validatedIds)->update([
            'status' => 'pending'
        ]);
        return redirect('permintaan_barang');
    }

    public function waiting(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        PermintaanBarang::whereIn('id_permintaan_barang', $validatedIds)->update([
            'status' => 'waiting'
        ]);
        return redirect('permintaan_barang');
    }

    public function approved(Request $request) {
        // Convert comma-separated string to array
        $ids = explode(',', $request->ids);

        // Validate that each element in the array is an integer
        $validatedIds = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        PermintaanBarang::whereIn('id_permintaan_barang', $validatedIds)->update([
            'status' => 'approved'
        ]);
        return redirect('permintaan_barang');
    }
}
