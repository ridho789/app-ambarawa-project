<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembangunan extends Model
{
    use HasFactory;
    protected $table = 'tbl_pembangunan';
    protected $primaryKey = 'id_pembangunan';

    protected $fillable = [
        'id_proyek',
        'ket',
        'tanggal',
        'nama',
        'ukuran',
        'deskripsi',
        'jumlah',
        'satuan',
        'harga',
        'tot_harga',
    ];
}
