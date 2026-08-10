<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarUsuarioActivo
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_active) {
            return response()->json(['mensaje' => 'Su cuenta ha sido deshabilitada por el administrador. Contacte a soporte.'], 401);
        }
        return $next($request);
    }
}
