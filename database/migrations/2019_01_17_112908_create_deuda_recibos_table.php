<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeudaRecibosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deuda_recibos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idRecibo')->unsigned();
            $table->integer('idDeuda')->unsigned();
            $table->foreign('idRecibo')->references('id')->on('recibos');
            $table->foreign('idDeuda')->references('id')->on('deuda_de_medidores');
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
        Schema::dropIfExists('deuda_recibos');
    }
}
