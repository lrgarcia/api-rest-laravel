<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
     public function prueba(Request $request){
        return "Accion de prueba Post Controller";
    }
}
