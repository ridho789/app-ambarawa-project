<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriMaterial extends Model
{
    use HasFactory;
    protected $table = 'tbl_kategori_material';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama'
    ];
}
