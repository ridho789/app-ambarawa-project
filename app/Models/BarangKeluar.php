<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;
    protected $table = 'tbl_barang_keluar';
    protected $primaryKey = 'id_barang_keluar';

    protected $fillable = [
        'id_stok_barang',
        'id_kendaraan',
        'tanggal_keluar',
        'pengguna',
        'jumlah',
        'sisa_stok',
        'lokasi',
        'ket'
    ];
}
