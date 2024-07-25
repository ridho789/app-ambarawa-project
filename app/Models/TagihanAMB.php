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
        'keterangan',
        'lokasi',
        'nopol',
        'kode_unit',
        'merk',
        'pemesan',
        'tgl_order',
        'tgl_invoice',
        'no_inventaris',
        'nama',
        'kategori',
        'dipakai_untuk',
        'masa_pakai',
        'jml',
        'unit',
        'harga',
        'harga_online',
        'ongkir',
        'asuransi',
        'b_proteksi',
        'b_jasa_aplikasi',
        'diskon_ongkir',
        'total',
        'toko'
    ];
}
