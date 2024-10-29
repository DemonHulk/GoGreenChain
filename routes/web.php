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
    Route::get('/usuario', [UsuarioControlador::class, 'index'])->name('usuario.index');

    Route::get('/ver_tareas', [UsuarioControlador::class, 'ver_tareas_usuario'])->name('usuario.perfil.ver_tareas');
    Route::get('/mis_tareas', [UsuarioControlador::class, 'mis_tareas_usuario'])->name('usuario.perfil.mis_tareas');
    Route::get('/mi_historial_tareas', [UsuarioControlador::class, 'mi_historial_usuario'])->name('usuario.perfil.mi_historial_tareas');

    Route::put('/tareas/aceptar/{id}', [UsuarioControlador::class, 'aceptar_tarea_usuario'])->name('tarea.aceptar');

    Route::get('/tareas/{id}', [UsuarioControlador::class, 'getTaskDetails'])->name('tareas.detalle');


});



//--------------------------------------------RUTAS PARA EMPRESA----------------------------------------------

//Grupo de rutas prefijas con Empresa para el controlador de Rol
Route::prefix('empresa/perfil')->middleware(['auth', 'can:Empresa'])->group(function () {
    // Ruta para mostrar todos los roles
    Route::get('/mi_empresa', [UsuarioControlador::class, 'mi_empresa'])->name('empresa.perfil.mi_empresa');
    Route::post('/usuario/send-near', [UsuarioControlador::class, 'sendNear'])->name('send.near');
    Route::put('/empresa/{id}', [UsuarioControlador::class, 'actualizar_empresa'])->name('empresa.actualizar_empresa');

    Route::get('/registrar_tarea', [UsuarioControlador::class, 'registrar_tarea'])->name('empresa.perfil.registrar_tarea');
    Route::get('/ver_tareas', [UsuarioControlador::class, 'ver_tareas'])->name('empresa.perfil.ver_tareas');
    Route::get('/empresa/perfil/tarea/{id}', [UsuarioControlador::class, 'obtenerTarea'])->name('empresa.perfil.obtenerTarea');



    Route::post('/guardar_tarea', [UsuarioControlador::class, 'guardar_tarea'])->name('guardar_tarea');


});

//Grupo de rutas prefijas con Empresa para el controlador de Rol
Route::prefix('empresa/walletNear')->middleware(['auth', 'can:Empresa'])->group(function () {


    Route::get('/near', [UsuarioControlador::class, 'near_vista'])->name('empresa.walletNear.near');


});