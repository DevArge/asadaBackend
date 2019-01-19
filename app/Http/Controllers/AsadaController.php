<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AsadaController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
    }
    
}
