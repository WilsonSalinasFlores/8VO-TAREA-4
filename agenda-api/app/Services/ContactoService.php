<?php
namespace App\Services;

use App\Repositories\Contracts\ContactoRepositoryInterface;
use Illuminate\Validation\ValidationException;

class ContactoService extends BaseService
{
    protected $contactoRepo;

    public function __construct(ContactoRepositoryInterface $contactoRepo)
    {
        $this->contactoRepo = $contactoRepo;
    }

    public function listar(int $usuarioId)
    {
        return $this->contactoRepo->obtenerActivos($usuarioId);
    }

    public function crear(int $usuarioId, array $datos)
    {
        $datos['usuario_id'] = $usuarioId;
        return $this->contactoRepo->guardar($datos);
    }

    public function actualizar(int $id, int $usuarioId, array $datos)
    {
        $contacto = $this->contactoRepo->buscarPorId($id);
        
        if (!$contacto || $contacto->usuario_id !== $usuarioId || $contacto->eliminado) {
            throw ValidationException::withMessages(['mensaje' => 'Contacto no encontrado o sin permisos.']);
        }

        return $this->contactoRepo->actualizar($id, $datos);
    }

    public function eliminar(int $id, int $usuarioId)
    {
        $contacto = $this->contactoRepo->buscarPorId($id);
        
        if (!$contacto || $contacto->usuario_id !== $usuarioId || $contacto->eliminado) {
            throw ValidationException::withMessages(['mensaje' => 'Contacto no encontrado o sin permisos.']);
        }

        return $this->contactoRepo->softDelete($id);
    }

    public function obtener(int $id, int $usuarioId)
    {
        $contacto = $this->contactoRepo->buscarPorId($id);
        
        if (!$contacto || $contacto->usuario_id !== $usuarioId || $contacto->eliminado) {
            throw ValidationException::withMessages(['mensaje' => 'Contacto no encontrado o sin permisos.']);
        }

        return $contacto;
    }
}
