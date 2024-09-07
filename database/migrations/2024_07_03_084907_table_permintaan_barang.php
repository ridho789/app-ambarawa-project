<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TablePermintaanBarang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_permintaan_barang', function (Blueprint $table) {
            $table->id('id_permintaan_barang');
            $table->string('noform');
            $table->string('nama');
            $table->string('jabatan');
            $table->string('kategori');
            $table->string('sub_kategori')->nullable();
            $table->date('tgl_order');
            $table->string('kegunaan');
            $table->string('status')->default('waiting');
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
        Schema::dropIfExists('tbl_permintaan_barang');
    }
}
