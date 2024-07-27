<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnForeignTablePembangunan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_pembangunan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_proyek')->nullable()->after('id_pembangunan');
            $table->foreign('id_proyek')->references('id_proyek')->on('tbl_proyek');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_pembangunan', function (Blueprint $table) {
            $table->dropForeign(['id_proyek']);
            $table->dropColumn('id_proyek');
        });
    }
}
