<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_operasional', function (Blueprint $table) {
            $table->id('id_operasional');
            $table->date('tanggal');
            $table->string('uraian');
            $table->string('deskripsi');
            $table->string('nama');
            $table->string('metode_pembelian')->nullable();
            $table->string('diskon')->nullable();
            $table->string('ongkir')->nullable();
            $table->string('asuransi')->nullable();
            $table->string('b_proteksi')->nullable();
            $table->string('p_member')->nullable();
            $table->string('b_aplikasi')->nullable();
            $table->string('total');
            $table->string('toko');
            $table->string('status')->default('processing');
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_operasional');
    }
};
