<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMorososTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('morosos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idAbonado')->unsigned();
            $table->integer('idRecibo')->unsigned();
            $table->string('estado');
            $table->foreign('idAbonado')->references('id')->on('abonados');
            $table->foreign('idRecibo')->references('id')->on('recibos');
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
        Schema::dropIfExists('morosos');
    }
}
