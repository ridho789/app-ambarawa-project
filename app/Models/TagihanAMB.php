<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanAMB extends Model
{
    use HasFactory;
    protected $table = 'tbl_tagihan_amb';
    protected $primaryKey = 'id_tagihan_amb';

    protected $fillable = [
        'id_kendaraan',
        'id_satuan',
        'id_toko',
        'keterangan',
        'noform',
        'lokasi',
        'pemesan',
        'tgl_order',
        'tgl_invoice',
        'no_inventaris',
        'nama',
        'kategori',
        'dipakai_untuk',
        'masa_pakai',
        'jml',
        'harga',
        'harga_online',
        'ongkir',
        'asuransi',
        'b_proteksi',
        'b_jasa_aplikasi',
        'diskon_ongkir',
        'total',
        'via',
        'status',
        'file'
    ];

    public function permintaanBarang() {
        return $this->hasMany(PermintaanBarang::class, 'noform', 'noform');
    }
}
