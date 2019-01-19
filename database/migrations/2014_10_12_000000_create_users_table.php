<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idAsada')->unsigned();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->enum('role', ['ADMIN_ROLE', 'FONTANERO_ROLE', 'SECRETARIA_ROLE']);
            $table->string('password');
            $table->foreign('idAsada')->references('id')->on('asadas');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('users');
    }
}
