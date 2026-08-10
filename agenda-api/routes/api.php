<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RecuperacionController;
use App\Http\Controllers\Api\Auth\PrimerLoginController;
use App\Http\Controllers\Api\ContactoController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CatalogoController;

// Public routes
Route::post('/auth/registro', [AuthController::class, 'registro']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/catalogo/preguntas', [CatalogoController::class, 'preguntas']);
Route::get('/auth/recuperar/preguntas', [RecuperacionController::class, 'obtenerPreguntas']);
Route::post('/auth/recuperar/verificar', [RecuperacionController::class, 'verificar']);
Route::post('/auth/recuperar/restablecer', [RecuperacionController::class, 'restablecer']);

// Authenticated routes
Route::middleware(['auth:sanctum', 'usuario.activo'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::apiResource('contactos', ContactoController::class);
    
    // Superuser routes
    Route::middleware('es.superusuario')->group(function () {
        Route::post('/auth/primer-login', [PrimerLoginController::class, 'cambiarPassword'])->withoutMiddleware('usuario.activo');
        Route::post('/auth/primer-login/preguntas', [PrimerLoginController::class, 'registrarPreguntas'])->withoutMiddleware('usuario.activo');
        
        Route::get('/admin/usuarios', [AdminController::class, 'listarUsuarios']);
        Route::get('/admin/usuarios/{id}/agenda', [AdminController::class, 'verAgenda']);
        Route::patch('/admin/usuarios/{id}/estado', [AdminController::class, 'cambiarEstado']);
        Route::get('/admin/bitacora', [AdminController::class, 'bitacora']);
    });
});
