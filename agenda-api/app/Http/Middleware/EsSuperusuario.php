<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EsSuperusuario
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->rol !== 'superusuario') {
            return response()->json(['mensaje' => 'Acceso no autorizado.'], 403);
        }
        return $next($request);
    }
}
