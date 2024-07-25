<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operasional extends Model
{
    use HasFactory;
    protected $table = 'tbl_operasional';
    protected $primaryKey = 'id_operasional';

    protected $fillable = [
        'tanggal',
        'uraian',
        'qty',
        'unit',
        'deskripsi',
        'nama',
        'harga_toko',
        'diskon',
        'harga_onl',
        'ongkir',
        'asuransi',
        'b_proteksi',
        'p_member',
        'b_aplikasi',
        'total',
        'toko'
    ];
}
