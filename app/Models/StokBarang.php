<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    use HasFactory;
    protected $table = 'tbl_stok_barang';
    protected $primaryKey = 'id_stok_barang';

    protected $fillable = [
        'id_satuan',
        'nama',
        'merk',
        'type',
        'kategori',
        'jumlah',
        'no_rak',
        'keterangan',
        'foto'
    ];
}
