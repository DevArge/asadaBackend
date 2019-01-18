<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecibosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idAbonado')->unsigned();
            $table->integer('idMedidor')->unsigned();
            $table->integer('idLectura')->unsigned();
            $table->integer('idAsada')->unsigned();
            $table->integer('idConfiguracionRecibos')->unsigned();
            $table->string('periodo');
            $table->enum('estado', ['PAGADO', 'PENDIENTE']);
            $table->decimal('reparacion', 10, 2)->nullable();
            $table->decimal('abonoMedidor', 10, 2)->nullable();
            $table->decimal('reactivacionMedidor', 10, 2)->nullable();
            $table->decimal('retrasoPago', 10, 2)->nullable();
            $table->integer('metrosConsumidos');
            $table->decimal('cargoFijo', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('hidrante', 10, 2);
            $table->integer('valorMetro');
            $table->date('vence');
            $table->foreign('idAbonado')->references('id')->on('abonados');
            $table->foreign('idMedidor')->references('id')->on('medidores');
            $table->foreign('idLectura')->references('id')->on('lecturas');
            $table->foreign('idAsada')->references('id')->on('asadas');
            $table->foreign('idConfiguracionRecibos')->references('id')->on('configuracion_recibos');
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
        Schema::dropIfExists('recibos');
    }
}
