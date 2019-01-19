<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MorosoController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
    }
    
}
