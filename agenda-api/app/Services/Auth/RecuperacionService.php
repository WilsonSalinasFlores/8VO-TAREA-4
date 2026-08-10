<?php
namespace App\Services\Auth;

use App\Services\BaseService;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Contracts\PreguntaRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RecuperacionService extends BaseService
{
    protected $usuarioRepo;
    protected $preguntaRepo;

    public function __construct(UsuarioRepositoryInterface $usuarioRepo, PreguntaRepositoryInterface $preguntaRepo)
    {
        $this->usuarioRepo = $usuarioRepo;
        $this->preguntaRepo = $preguntaRepo;
    }

    public function obtenerPreguntas(string $cedula)
    {
        $usuario = $this->usuarioRepo->buscarPorCedula($cedula);
        if (!$usuario) {
            throw ValidationException::withMessages(['mensaje' => 'Usuario no encontrado.']);
        }

        return $this->preguntaRepo->obtenerDeUsuario($usuario->id);
    }

    public function verificarRespuestas(array $datos)
    {
        $usuario = $this->usuarioRepo->buscarPorCedula($datos['cedula']);
        if (!$usuario) {
            throw ValidationException::withMessages(['mensaje' => 'Usuario no encontrado.']);
        }

        if (!$this->preguntaRepo->verificarRespuestas($usuario->id, $datos['respuestas'])) {
            throw ValidationException::withMessages(['mensaje' => 'Respuestas incorrectas.']);
        }

        return $usuario;
    }

    public function restablecerPassword(array $datos)
    {
        $usuario = $this->verificarRespuestas($datos);
        
        if (!isset($datos['password_nuevo']) || strlen($datos['password_nuevo']) < 8) {
            throw ValidationException::withMessages(['mensaje' => 'El nuevo password no es válido.']);
        }

        $this->usuarioRepo->actualizar($usuario->id, [
            'password' => Hash::make($datos['password_nuevo'])
        ]);

        return true;
    }
}
