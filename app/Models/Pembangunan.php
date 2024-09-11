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
        'id_satuan',
        'id_kategori',
        'id_toko',
        'noform',
        'ket',
        'tanggal',
        'nama',
        'ukuran',
        'deskripsi',
        'jumlah',
        'harga',
        'tot_harga',
        'status',
        'file'
    ];

    public function permintaanBarang() {
        return $this->hasMany(PermintaanBarang::class, 'noform', 'noform');
    }
}
