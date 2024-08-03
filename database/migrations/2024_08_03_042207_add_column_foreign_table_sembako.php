<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnForeignTableSembako extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_sembako', function (Blueprint $table) {
            $table->unsignedBigInteger('id_satuan')->nullable()->after('id_sembako');
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
        Schema::table('tbl_sembako', function (Blueprint $table) {
            $table->dropForeign(['id_satuan']);
            $table->dropColumn('id_satuan');
        });
    }
}
