<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCuentasPorPagarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idAsada')->unsigned();
            $table->string('nombre');
            $table->string('codigo');
            $table->string('description');
            $table->enum('tipo', ['INVERSION', 'GASTO']);
            $table->decimal('presupuesto', 12,2)->nullable();
            $table->foreign('idAsada')->references('id')->on('asadas');
            $table->softDeletes();
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
        Schema::dropIfExists('cuentas');
    }
}
