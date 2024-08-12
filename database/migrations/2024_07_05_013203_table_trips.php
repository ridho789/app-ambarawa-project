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
        Schema::create('tbl_trips', function (Blueprint $table) {
            $table->id('id_trip');
            $table->date('tanggal');
            $table->string('kota');
            $table->string('ket');
            $table->string('uraian');
            $table->string('qty');
            $table->string('unit');
            $table->string('km_awal');
            $table->string('km_isi_seb');
            $table->string('km_isi_sek');
            $table->string('km_akhir');
            $table->string('km_ltr')->nullable();
            $table->string('harga');
            $table->string('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_trips');
    }
};
