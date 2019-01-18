<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLecturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lecturas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idMedidor')->unsigned();
            $table->integer('lectura');
            $table->decimal('promedio', 10, 2);
            $table->string('periodo');
            $table->string('nota', 200)->nullable();
            $table->integer('metros');
            $table->foreign('idMedidor')->references('id')->on('medidores');
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
        Schema::dropIfExists('lecturas');
    }
}
