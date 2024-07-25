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
        Schema::create('tbl_tagihan_amb', function (Blueprint $table) {
            $table->id('id_tagihan_amb');
            $table->string('keterangan');
            $table->string('lokasi');
            $table->string('nopol')->nullable();
            $table->string('kode_unit')->nullable();
            $table->string('merk')->nullable();
            $table->string('pemesan');
            $table->date('tgl_order');
            $table->date('tgl_invoice');
            $table->string('no_inventaris');
            $table->string('nama');
            $table->string('kategori');
            $table->string('dipakai_untuk');
            $table->string('masa_pakai');
            $table->string('jml')->nullable();
            $table->string('unit')->nullable();
            $table->string('harga')->nullable();
            $table->string('harga_online')->nullable();
            $table->string('ongkir')->nullable();
            $table->string('asuransi')->nullable();
            $table->string('b_proteksi')->nullable();
            $table->string('b_jasa_aplikasi')->nullable();
            $table->string('diskon_ongkir')->nullable();
            $table->string('total');
            $table->string('toko');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_tagihan_amb');
    }
};
