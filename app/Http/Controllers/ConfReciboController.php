<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfReciboController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
    }
    
}
