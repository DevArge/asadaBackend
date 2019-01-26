<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $id = DB::table('asadas')->insertGetId([
            'nombre'          => 'ASADA',
            'cedulaJuridica'  => '1-2345678',
            'direccion'       => 'direccion de ASADA',
            'telefono'        => '12345678',
        ]);

        DB::table('users')->insert([
            'idAsada'         => $id,
            'nombre'          => 'Administrador',
            'email'           => 'sifcamail@gmail.com',
            'role'            => 'ADMIN_ROLE',
            'password'        => bcrypt('qwerty'),
        ]);

        
        DB::table('tipo_de_medidores')->insert([
            'nombre'          => 'Habitacional',
            'precio'          => 3000
        ]);

        DB::table('configuracion_de_medidores')->insert([
            'impuestoHidrante'     => 12,
            'unoAdiez'             => 200,
            'onceAtreinta'         => 215,
            'treintaYunoAsecenta'  => 230,
            'masDeSecenta'         => 245,
            'impuestoReactivacion' => 1000
          ]);


        DB::table('configuracion_recibos')->insert([
            'impuestoRetraso' => 0,
            'notificacion'    => '',
            'notificacionDefault'=> 'A partir del dia siguiente a la fecha de vencimiento del mes que esta al cobro, se le concede tres dias habiles para su cancelacion con recargo, si en dicho plazo no cumple, se procede en cualquier momento a suspender el servicio.',
            'fechaInicio'     => Carbon\Carbon::now(),
            'fechaFin'        => Carbon\Carbon::now()
        ]);
    }
}
