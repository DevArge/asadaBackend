<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacturasCuentasPorPagarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idCuenta')->unsigned();
            $table->string('numero');
            $table->string('descripcion');
            $table->date('fecha');
            $table->decimal('sub_total', 12,2);
            $table->decimal('descuento', 12,2)->nullable();
            $table->decimal('grand_total', 12,2);
            $table->foreign('idCuenta')->references('id')->on('cuentas');
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
        Schema::dropIfExists('facturas');
    }
}
