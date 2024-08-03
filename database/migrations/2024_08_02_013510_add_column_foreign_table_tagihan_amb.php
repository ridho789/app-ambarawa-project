<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnForeignTableTagihanAmb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_tagihan_amb', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kendaraan')->nullable()->after('id_tagihan_amb');
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('tbl_kendaraan');

            $table->unsignedBigInteger('id_satuan')->nullable()->after('id_kendaraan');
            $table->foreign('id_satuan')->references('id_satuan')->on('tbl_satuan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_tagihan_amb', function (Blueprint $table) {
            $table->dropForeign(['id_kendaraan']);
            $table->dropColumn('id_kendaraan');

            $table->dropForeign(['id_satuan']);
            $table->dropColumn('id_satuan');
        });
    }
}
