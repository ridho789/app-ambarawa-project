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
            $table->string('ket');
            $table->date('tanggal');
            $table->string('nama');
            $table->string('ukuran');
            $table->string('deskripsi');
            $table->string('jumlah');
            $table->string('satuan');
            $table->string('harga');
            $table->string('tot_harga');
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
