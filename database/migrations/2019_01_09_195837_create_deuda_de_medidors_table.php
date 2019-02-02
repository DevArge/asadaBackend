<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeudaDeMedidorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deuda_de_medidores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idMedidor')->unsigned();
            $table->decimal('costoTotal', 8, 2);
            $table->decimal('deuda', 8, 2);
            $table->integer('plazo');
            $table->enum('tipoDeuda', ['REPARACION', 'ABONO', 'REACTIVACION']);
            $table->string('estado');
            $table->string('detalleDeuda')->nullable();
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
        Schema::dropIfExists('deuda_de_medidores');
    }
}
