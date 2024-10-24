<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioControlador;

Route::get('', [UsuarioControlador::class, 'index']);