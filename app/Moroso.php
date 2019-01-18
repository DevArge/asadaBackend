<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Moroso extends Model{

    protected $table = 'morosos';
    protected $fillable = ['idAbonado', 'idRecibo', 'estado'];
}
