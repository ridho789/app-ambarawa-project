<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableBarangKeluar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_barang_keluar', function (Blueprint $table) {
            $table->id('id_barang_keluar');
            $table->date('tanggal_keluar');
            $table->string('pengguna');
            $table->string('jumlah');
            $table->string('sisa_stok');
            $table->string('lokasi')->nullable();
            $table->string('ket')->nullable();
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
        Schema::dropIfExists('tbl_barang_keluar');
    }
}
