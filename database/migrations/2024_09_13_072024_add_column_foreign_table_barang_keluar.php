<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnForeignTableBarangKeluar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_barang_keluar', function (Blueprint $table) {
            $table->unsignedBigInteger('id_stok_barang')->nullable()->after('id_barang_keluar');
            $table->foreign('id_stok_barang')->references('id_stok_barang')->on('tbl_stok_barang');

            $table->unsignedBigInteger('id_kendaraan')->nullable()->after('id_stok_barang');
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('tbl_kendaraan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_barang_keluar', function (Blueprint $table) {
            $table->dropForeign(['id_stok_barang']);
            $table->dropColumn('id_stok_barang');

            $table->dropForeign(['id_kendaraan']);
            $table->dropColumn('id_kendaraan');
        });
    }
}
