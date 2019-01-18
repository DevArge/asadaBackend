<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedidorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medidores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idAbonado')->unsigned();
            $table->integer('idTipoDeMedidor')->unsigned();
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->string('detalle')->nullable();
            $table->foreign('idAbonado')->references('id')->on('abonados');
            $table->foreign('idTipoDeMedidor')->references('id')->on('tipo_de_medidores');
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
        Schema::dropIfExists('medidores');
    }
}
