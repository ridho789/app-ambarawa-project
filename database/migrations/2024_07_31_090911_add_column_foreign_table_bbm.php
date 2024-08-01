<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnForeignTableBbm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_bbm', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kendaraan')->nullable()->after('id_bbm');
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
        Schema::table('tbl_bbm', function (Blueprint $table) {
            $table->dropForeign(['id_kendaraan']);
            $table->dropColumn('id_kendaraan');
        });
    }
}
