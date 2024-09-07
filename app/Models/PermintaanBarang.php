<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    use HasFactory;
    protected $table = 'tbl_permintaan_barang';
    protected $primaryKey = 'id_permintaan_barang';

    protected $fillable = [
        'noform',
        'nama',
        'jabatan',
        'kategori',
        'sub_kategori',
        'tgl_order',
        'kegunaan',
        'status'
    ];
}
