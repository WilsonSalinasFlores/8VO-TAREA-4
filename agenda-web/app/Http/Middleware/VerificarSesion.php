<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarSesion
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('token')) {
            return redirect('/login')->with('error', 'Debe iniciar sesión primero.');
        }

        return $next($request);
    }
}
