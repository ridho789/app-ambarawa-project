<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;
    protected $table = 'tbl_barang_masuk';
    protected $primaryKey = 'id_barang_masuk';

    protected $fillable = [
        'id_barang',
        'tanggal_order',
        'tanggal_masuk',
        'jumlah',
    ];
}
