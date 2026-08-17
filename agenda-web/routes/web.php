<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AdminController;

Route::middleware(\App\Http\Middleware\VerificarSesion::class)->group(function () {
    Route::get('/agenda', [AgendaController::class, 'index']);
    Route::get('/agenda/nuevo', [AgendaController::class, 'create']);
    Route::post('/agenda', [AgendaController::class, 'store']);
    Route::get('/agenda/{id}/editar', [AgendaController::class, 'edit']);
    Route::put('/agenda/{id}', [AgendaController::class, 'update']);
    Route::delete('/agenda/{id}', [AgendaController::class, 'destroy']);
    
    Route::middleware(\App\Http\Middleware\VerificarAdmin::class)->group(function () {
        Route::get('/admin/usuarios', [AdminController::class, 'usuarios']);
        Route::post('/admin/usuarios/{id}/estado', [AdminController::class, 'cambiarEstado']);
        Route::get('/admin/usuarios/{id}/agenda', [AdminController::class, 'agenda']);
        Route::get('/admin/bitacora', [AdminController::class, 'bitacora']);
    });
});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/registro', [AuthController::class, 'registroForm']);
Route::post('/registro', [AuthController::class, 'registro']);
Route::get('/recuperar', [AuthController::class, 'recuperarForm']);
Route::post('/recuperar', [AuthController::class, 'recuperar']);

Route::middleware(\App\Http\Middleware\VerificarSesion::class)->group(function() {
    Route::get('/primer-login', [AuthController::class, 'primerLoginForm']);
    Route::post('/primer-login', [AuthController::class, 'primerLogin']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/', fn() => redirect('/login'));
