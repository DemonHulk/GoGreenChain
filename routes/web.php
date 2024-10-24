<?php

use App\Http\Controllers\CobrosConceptoControlador;
use App\Http\Controllers\CobrosControlador;
use App\Http\Controllers\ConceptosControlador;
use App\Http\Controllers\CondonacionesControlador;
use App\Http\Controllers\ContratosControlador;
use App\Http\Controllers\CreditosControlador;
use App\Http\Controllers\DatosFiscalesControlador;
use App\Http\Controllers\FacturasControlador;
use App\Http\Controllers\NearController;
use App\Http\Controllers\RolControlador;
use App\Http\Controllers\TiposContratoControlador;
use App\Http\Controllers\UmaControlador;
use App\Http\Controllers\UsuarioControlador;
use App\Models\DatosFiscalesModelo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Facade;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


//--------------------------------------------RUTAS PARA ADMINISTRADOR----------------------------------------------

//Grupo de rutas prefijas con admin para el controlador de Rol
Route::prefix('admin/roles')->middleware(['auth', 'can:Administrador'])->group(function () {
   
});





//--------------------------------------------RUTAS PARA USUARIO----------------------------------------------

//Grupo de rutas prefijas con Usuario para el controlador de Rol
Route::prefix('usuario/perfil')->middleware(['auth', 'can:Usuario'])->group(function () {
    // Ruta para mostrar todos los roles
    Route::get('/mi_perfil', [UsuarioControlador::class, 'show'])->name('usuario.perfil.mi_perfil');
    Route::put('/mi_perfil/{id}', [UsuarioControlador::class, 'update'])->name('usuario.perfil.actualizar');

});



//--------------------------------------------RUTAS PARA EMPRESA----------------------------------------------

//Grupo de rutas prefijas con Empresa para el controlador de Rol
Route::prefix('empresa/perfil')->middleware(['auth', 'can:Empresa'])->group(function () {
    // Ruta para mostrar todos los roles
    Route::get('/mi_empresa', [UsuarioControlador::class, 'mi_empresa'])->name('empresa.perfil.mi_empresa');
    Route::put('/mi_perfil/{id}', [UsuarioControlador::class, 'update'])->name('empresa.perfil.actualizar');
    Route::post('/usuario/send-near', [UsuarioControlador::class, 'sendNear'])->name('send.near');
    Route::put('/empresa/{id}', [UsuarioControlador::class, 'actualizar_empresa'])->name('empresa.actualizar_empresa');

    Route::get('/registrar_tarea', [UsuarioControlador::class, 'registrar_tarea'])->name('empresa.perfil.registrar_tarea');
    Route::get('/ver_tareas', [UsuarioControlador::class, 'ver_tareas'])->name('empresa.perfil.ver_tareas');
    Route::post('/guardar_tarea', [UsuarioControlador::class, 'guardar_tarea'])->name('guardar_tarea');

});

