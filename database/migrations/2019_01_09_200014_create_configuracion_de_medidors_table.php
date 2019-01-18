<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfiguracionDeMedidorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracion_de_medidores', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('impuestoHidrante', 12,2);
            $table->integer('unoAdiez');
            $table->integer('onceAtreinta');
            $table->integer('treintaYunoAsecenta');
            $table->integer('masDeSecenta');
            $table->integer('impuestoReactivacion');
            $table->integer('impuestoRetraso');
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
        Schema::dropIfExists('configuracion_de_medidores');
    }
}
