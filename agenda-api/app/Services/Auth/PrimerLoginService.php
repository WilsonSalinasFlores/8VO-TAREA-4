<?php
namespace App\Services\Auth;

use App\Services\BaseService;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Contracts\PreguntaRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PrimerLoginService extends BaseService
{
    protected $usuarioRepo;
    protected $preguntaRepo;

    public function __construct(UsuarioRepositoryInterface $usuarioRepo, PreguntaRepositoryInterface $preguntaRepo)
    {
        $this->usuarioRepo = $usuarioRepo;
        $this->preguntaRepo = $preguntaRepo;
    }

    public function cambiarPassword($usuario, array $datos)
    {
        if (!Hash::check($datos['password_actual'], $usuario->password)) {
            throw ValidationException::withMessages(['mensaje' => 'Contraseña actual incorrecta.']);
        }

        $this->usuarioRepo->actualizar($usuario->id, [
            'password' => Hash::make($datos['password_nuevo']),
            'primer_login' => 0
        ]);
    }

    public function registrarPreguntas($usuario, array $datos)
    {
        $preguntas = array_map(function($p) use ($usuario) {
            return [
                'usuario_id' => $usuario->id,
                'catalogo_pregunta_id' => $p['id'],
                'respuesta' => strtolower(trim($p['respuesta'])),
            ];
        }, $datos['preguntas']);

        $this->preguntaRepo->guardarPreguntas($preguntas);
    }
}
