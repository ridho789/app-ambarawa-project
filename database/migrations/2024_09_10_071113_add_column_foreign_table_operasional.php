<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnForeignTableOperasional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_operasional', function (Blueprint $table) {
            $table->unsignedBigInteger('id_toko')->nullable()->after('id_operasional');
            $table->foreign('id_toko')->references('id_toko')->on('tbl_toko');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_operasional', function (Blueprint $table) {
            $table->dropForeign(['id_toko']);
            $table->dropColumn('id_toko');
        });
    }
}
