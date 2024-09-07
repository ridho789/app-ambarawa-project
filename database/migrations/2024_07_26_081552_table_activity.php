<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_activity', function (Blueprint $table) {
            $table->id('id_activity');
            $table->unsignedBigInteger('id_relation')->nullable();
            $table->string('description');
            $table->string('scope');
            $table->string('action');
            $table->string('user');
            $table->dateTime('action_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_activity');
    }
}
