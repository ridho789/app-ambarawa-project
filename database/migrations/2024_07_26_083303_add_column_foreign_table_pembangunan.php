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

            $table->unsignedBigInteger('id_satuan')->nullable()->after('id_proyek');
            $table->foreign('id_satuan')->references('id_satuan')->on('tbl_satuan');

            $table->unsignedBigInteger('id_kategori')->nullable()->after('id_satuan');
            $table->foreign('id_kategori')->references('id_kategori')->on('tbl_kategori_material');
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

            $table->dropForeign(['id_satuan']);
            $table->dropColumn('id_satuan');

            $table->dropForeign(['id_kategori']);
            $table->dropColumn('id_kategori');
        });
    }
}
