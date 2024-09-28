<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableStokBarang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stok_barang', function (Blueprint $table) {
            $table->id('id_stok_barang');
            $table->string('nama');
            $table->string('kategori')->nullable();
            $table->string('merk')->nullable();
            $table->string('type')->nullable();
            $table->string('jumlah');
            $table->string('no_rak')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_stok_barang');
    }
}
