<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistroRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use App\Http\Resources\UsuarioResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function registro(RegistroRequest $request)
    {
        $usuario = $this->authService->registro($request->validated());
        return response()->json([
            'exito' => true,
            'mensaje' => 'Registro exitoso',
            'data' => ['usuario' => new UsuarioResource($usuario)]
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $resultado = $this->authService->login($request->validated());
        return response()->json([
            'exito' => true,
            'data' => [
                'token' => $resultado['token'],
                'user' => new UsuarioResource($resultado['usuario']),
                'primer_login' => $resultado['primer_login']
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return response()->json(['exito' => true, 'mensaje' => 'Sesión cerrada exitosamente']);
    }
}
