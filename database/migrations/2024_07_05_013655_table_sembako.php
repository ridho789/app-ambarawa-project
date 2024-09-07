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
        Schema::create('tbl_sembako', function (Blueprint $table) {
            $table->id('id_sembako');
            $table->date('tanggal');
            $table->string('nama');
            $table->string('qty')->nullable();
            $table->string('harga')->nullable();
            $table->string('total');
            $table->string('status')->default('processing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sembako');
    }
};
