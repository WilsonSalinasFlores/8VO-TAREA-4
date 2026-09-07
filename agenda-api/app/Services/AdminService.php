<?php
namespace App\Services;

use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Contracts\ContactoRepositoryInterface;
use App\Repositories\Contracts\BitacoraRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AdminService extends BaseService
{
    protected $usuarioRepo;
    protected $contactoRepo;
    protected $bitacoraRepo;

    public function __construct(
        UsuarioRepositoryInterface $usuarioRepo,
        ContactoRepositoryInterface $contactoRepo,
        BitacoraRepositoryInterface $bitacoraRepo
    ) {
        $this->usuarioRepo = $usuarioRepo;
        $this->contactoRepo = $contactoRepo;
        $this->bitacoraRepo = $bitacoraRepo;
    }

    public function listarUsuarios()
    {
        return $this->usuarioRepo->listarTodos();
    }

    public function verAgenda(int $usuarioId)
    {
        return $this->contactoRepo->obtenerTodosDeUsuario($usuarioId);
    }

    public function cambiarEstado(int $usuarioId)
    {
        $usuario = $this->usuarioRepo->buscarPorId($usuarioId);
        if (!$usuario || $usuario->rol === 'superusuario') {
            throw ValidationException::withMessages(['mensaje' => 'Usuario inválido.']);
        }

        $nuevoEstado = !$usuario->is_active;
        $this->usuarioRepo->cambiarEstado($usuarioId, $nuevoEstado);

        $this->bitacoraRepo->registrar([
            'accion' => 'CAMBIO_ESTADO',
            'descripcion' => "Estado cambiado a " . ($nuevoEstado ? 'Activo' : 'Inactivo'),
            'target_usuario_id' => $usuarioId,
            'superusuario_id' => auth()->id(),
        ]);

        return $nuevoEstado;
    }

    public function listarBitacora(array $filtros = [], int $perPage = 10)
    {
        return $this->bitacoraRepo->listar($filtros, $perPage);
    }

    public function listarSesiones()
    {
        return PersonalAccessToken::with('tokenable')
            ->orderBy('last_used_at', 'desc')
            ->get();
    }
}
