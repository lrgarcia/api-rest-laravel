<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Middleware\ApiAuthMiddleware;
//RUTAS DE PRUEBA

Route::get('/', function () {
    return '<hi>"Hola mundo!"</h1>';
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/pruebas/{nombre?}', function ($nombre=null) {
    $texto='<h2>"Hola mundo!"</h2>';
    $texto.= "Nombre: ".$nombre;
    $patata='<h1>"Prueba de ruta patata"</h1>';
    return view('pruebas', array(
        'texto'=>$texto,
        'patata'=>$patata
            
    ));
});

/*Metodo HTTP comunes
    *GET: Conseguir datos o recursos
    *POST: Guardar datos o recursos o hacer logica desde un formulario
    *PUT: Actualizar datos o recursos   
    *DELETE: Eliminar datos o recursos   */


//Rutas de prueba
/*
Route::get('/animales','PruebasController@index');
Route::get('/test','PruebasController@testOrm');
Route::get('/usuario/prueba','UserController@prueba');*/
//Rutas del controlador de usuario
Route::post('api/register','UserController@register');
Route::post('api/login','UserController@login');
Route::put('api/user/update','UserController@update');
Route::post('api/user/upload','UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('api/user/avatar/{filename}','UserController@getImage');
Route::get('api/user/detail/{id}','UserController@detail');

//Rutas del controlador de de categorias

Route::resource('api/category','CategoryController');