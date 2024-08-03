<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BBM extends Model
{
    use HasFactory;
    protected $table = 'tbl_bbm';
    protected $primaryKey = 'id_bbm';

    protected $fillable = [
        'id_kendaraan',
        'nama',
        'tanggal',
        'liter',
        'km_awal',
        'km_isi',
        'km_akhir',
        'km_ltr',
        'harga',
        'tot_harga',
        'ket',
        'tot_km'
    ];
}
