<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = session('usuario');
        if (!$usuario || $usuario['rol'] !== 'superusuario') {
            return redirect('/agenda')->with('error', 'Acceso no autorizado.');
        }

        return $next($request);
    }
}
