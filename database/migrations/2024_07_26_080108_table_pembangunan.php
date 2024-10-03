<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TablePembangunan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pembangunan', function (Blueprint $table) {
            $table->id('id_pembangunan');
            $table->string('noform')->nullable();
            $table->string('ket');
            $table->date('tanggal')->nullable();
            $table->string('pemesan')->nullable();
            $table->string('kategori_barang')->nullable();
            $table->string('no_inventaris')->nullable();
            $table->string('masa_pakai')->nullable();
            $table->string('nama');
            $table->string('ukuran')->nullable();
            $table->string('deskripsi');
            $table->string('jumlah');
            $table->string('harga');
            $table->string('tot_harga');
            $table->string('status')->default('processing');
            $table->string('file')->nullable();
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
        Schema::dropIfExists('tbl_pembangunan');
    }
}
