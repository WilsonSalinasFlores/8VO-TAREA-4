<?php
namespace App\Services\Auth;

use App\Services\BaseService;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Contracts\PreguntaRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService extends BaseService
{
    protected $usuarioRepo;
    protected $preguntaRepo;

    public function __construct(UsuarioRepositoryInterface $usuarioRepo, PreguntaRepositoryInterface $preguntaRepo)
    {
        $this->usuarioRepo = $usuarioRepo;
        $this->preguntaRepo = $preguntaRepo;
    }

    public function login(array $credenciales)
    {
        $usuario = $this->usuarioRepo->buscarPorCedula($credenciales['cedula']);

        if (!$usuario || !Hash::check($credenciales['password'], $usuario->password)) {
            throw ValidationException::withMessages(['mensaje' => 'Credenciales incorrectas.']);
        }

        if (!$usuario->is_active) {
            throw ValidationException::withMessages(['mensaje' => 'Su cuenta ha sido deshabilitada por el administrador. Contacte a soporte.']);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'usuario' => $usuario,
            'primer_login' => (bool) $usuario->primer_login,
        ];
    }

    public function registro(array $datos)
    {
        $datos['password'] = Hash::make($datos['password']);
        $datos['rol'] = 'usuario';
        
        $usuario = $this->usuarioRepo->guardar($datos);

        $preguntas = array_map(function($p) use ($usuario) {
            return [
                'usuario_id' => $usuario->id,
                'catalogo_pregunta_id' => $p['id'],
                'respuesta' => strtolower(trim($p['respuesta'])),
            ];
        }, $datos['preguntas']);

        $this->preguntaRepo->guardarPreguntas($preguntas);

        return $usuario;
    }

    public function logout($usuario)
    {
        $usuario->currentAccessToken()->delete();
    }
}
