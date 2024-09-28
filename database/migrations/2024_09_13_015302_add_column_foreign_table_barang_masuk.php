<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnForeignTableBarangMasuk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_barang_masuk', function (Blueprint $table) {
            $table->unsignedBigInteger('id_barang')->nullable()->after('id_barang_masuk');
            $table->foreign('id_barang')->references('id_barang')->on('tbl_barang');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['id_barang']);
            $table->dropColumn('id_barang');
        });
    }
}
