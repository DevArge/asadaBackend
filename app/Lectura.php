<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Lectura;
use App\Recibo;
use Exception;
use DB;

class Lectura extends Model{
    
    protected $table = 'lecturas';
    protected $fillable = ['idMedidor', 'lectura',  'promedio', 'periodo', 'nota', 'metros'];

    public static function validarLectura($lectura, $idMedidor, $periodo){
        $lecturaAnterior = 0;
        $existeLectura = Lectura::existeLectura($idMedidor, $periodo);
        $lecturaPosterior = Lectura::lecturaPosterior($idMedidor, $periodo);
        $query = Lectura::lecturaAnterior($idMedidor, $periodo);
        if ($query != null ) {
            $lecturaAnterior = $query->lectura;
        }
        if ($existeLectura != null) { $existeLectura = $existeLectura->periodo; }
        if (!$lecturaAnterior) { $lecturaAnterior = 0; }
        if ($lecturaAnterior > $lectura) {
            return 'La lectura no puede ser menor a la anterior';
        }else if ($lecturaPosterior != null) {
           return 'No puede insertar, actualizar o eliminar una lectura cuando ya existe una lectura en un periodo posterior, para poder realizar esta acción la lectura tiene que ser la más reciente';
        }else if($existeLectura === $periodo){
            return 'actualizar lectura';
        }else{
            return 'nueva lectura';
        }
    }

    public static function guardarLectura($lectura, $idMedidor, $periodo, $nota='', $mensaje){
       return DB::transaction(function ()use($lectura, $idMedidor, $periodo,$nota, $mensaje) {
            $lecturaAnterior = Lectura::lecturaAnterior($idMedidor, $periodo) == null ? 0 : Lectura::lecturaAnterior($idMedidor, $periodo)->lectura;
            $metros = $lecturaAnterior == 0 ? $lectura : ($lectura - $lecturaAnterior);
            $lecturaModel = null;
            if ($mensaje == 'actualizar lectura') {
                $idLectura = Lectura::where('idMedidor', $idMedidor)->where('periodo', $periodo)->value('id');
                $lecturaModel = Lectura::find($idLectura);
                if ($lecturaModel->lectura == $lectura) {
                    $lecturaModel->nota = $nota;
                    $lecturaModel->save();
                    return $lecturaModel;
                }
            }else {// NUEVA LECTURA
                $lecturaModel = new Lectura();
                $lecturaModel->idMedidor = $idMedidor;
                $lecturaModel->periodo = $periodo;
            }
            $lecturaModel->lectura = $lectura;
            $lecturaModel->metros = $metros;
            $lecturaModel->nota = $nota;
            $lecturaModel->promedio = 0;
            $lecturaModel->save();
            $lecturaModel->promedio = Lectura::promedioTresMeses($periodo, $idMedidor);
            $lecturaModel->save();
            Recibo::crearRecibo($idMedidor, $lecturaModel->id, $periodo, $metros);
            return $lecturaModel;
        });
    }

    public static function obtenerLecturas($desde, $cantidad, $columna, $orden, $todos, $periodo){
        $query = Lectura::consultaSQL($periodo);
        return Lectura::paginar($desde, $cantidad, $columna, $orden, $query)->get();
    }

    public static function obtenerLecturasDeUnMedidor($desde, $cantidad, $columna, $orden, $idMedidor){
        $query = Lectura::lecturasDeUnMedidor($idMedidor);
        return Lectura::paginar($desde, $cantidad, $columna, $orden, $query)->get();
    }

    public static function paginar($desde, $cantidad, $columna, $orden, $query){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return $query->skip($desde)
                    ->take($cantidad)
                    ->orderBy($columna, $orden);
    }

    public static function buscarLecturasUnmedidor($termino, $idMedidor){
        return Lectura::lecturasDeUnMedidor($idMedidor)->where('lecturas.periodo', 'like', "%{$termino}%");
    }

    public static function buscarLecturas($termino, $periodo, $desde, $cantidad, $columna, $orden){
        $nombre = Lectura::consultaSQL($periodo)->where('abonados.nombre', 'like', "%{$termino}%");
        $cedula = Lectura::consultaSQL($periodo)->where('cedula', 'like', "%{$termino}%");
        $apellido1 = Lectura::consultaSQL($periodo)->where('apellido1', 'like', "%{$termino}%");
        $query = Lectura::consultaSQL($periodo)->where('apellido2', 'like', "%{$termino}%")
                ->union($nombre)
                ->union($cedula)
                ->union($apellido1);
        return Lectura::paginar($desde, $cantidad, $columna, $orden, $query);
    }

    public static function validarFormatoPeriodo($periodo){
        try{
            $fecha = Carbon::createFromFormat('Y-m-d', $periodo .'-01');
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    //======================== CONSULTAS SQL ===============================//

    public static function lecturaAnterior($idMedidor, $periodo){
        return DB::table('lecturas')
            ->select('lectura', 'periodo')
            ->where('idMedidor', $idMedidor)
            ->where('periodo', '<', $periodo)
            ->orderBy('lectura', 'desc')
            ->first();
    }

    public static function lecturasAnteriores($idMedidor, $periodo){
        return DB::table('lecturas')
            ->select('lectura', 'periodo', 'metros')
            ->where('idMedidor', $idMedidor)
            ->where('periodo', '<', $periodo)
            ->orderBy('lectura', 'desc')
            ->limit(3)
            ->get();

    }

    public static function existeLectura($idMedidor, $periodo){
        return DB::table('lecturas')
            ->select('lectura', 'periodo')
            ->where('idMedidor', $idMedidor)
            ->where('periodo', '=', $periodo)
            ->first();
    }

    public static function lecturaPosterior($idMedidor, $periodo){
        return DB::table('lecturas')
            ->select('lectura', 'periodo')
            ->where('idMedidor', $idMedidor)
            ->where('periodo', '>', $periodo)
            ->first();
    }

    public static function promedioTresMeses($periodo, $idMedidor){
        return DB::table('lecturas')
            ->where('periodo', '<=', $periodo)
            ->where('idMedidor', $idMedidor)
            ->orderBy('lectura', 'desc')
            ->limit(3)
            ->avg('metros');
    }

    public static function lecturasDeUnMedidor($idMedidor){
        return DB::table('abonados')
                ->join('medidores', 'abonados.id', '=', 'medidores.idAbonado')
                ->join('lecturas', 'medidores.id', '=', 'lecturas.idMedidor')
                ->select('abonados.id', 'nombre', 'apellido1', 'apellido2', 'lecturas.id as idLectura',
                    'medidores.id as medidor', 'lectura', 'metros', 'periodo')
                ->where('medidores.id', '=', $idMedidor);
    }

    public static function consultaSQL($periodo){
        return DB::table('abonados')
            ->join('medidores', 'abonados.id', '=', 'medidores.idAbonado')
            ->leftjoin(DB::raw("(select * from lecturas where periodo ='" . $periodo ."' ) lecturas"),
                function($join){
                    $join->on('lecturas.idMedidor', '=', 'medidores.id');
                })
            ->select('abonados.id','nombre', 'apellido1', 'apellido2', 'promedio',
            'medidores.id as medidor', 'detalle', 'lectura', 'metros', 'lecturas.id as idLectura')
            ->where('medidores.estado', '!=', 'INACTIVO');
    }

    

}
