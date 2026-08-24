<?php
namespace App\Services;

use App\Repositories\Contracts\ContactoRepositoryInterface;
use App\Repositories\Contracts\BitacoraRepositoryInterface;
use Illuminate\Validation\ValidationException;

class ContactoService extends BaseService
{
    protected $contactoRepo;
    protected $bitacoraRepo;

    public function __construct(
        ContactoRepositoryInterface $contactoRepo,
        BitacoraRepositoryInterface $bitacoraRepo
    ) {
        $this->contactoRepo = $contactoRepo;
        $this->bitacoraRepo = $bitacoraRepo;
    }

    public function listar(int $usuarioId)
    {
        return $this->contactoRepo->obtenerActivos($usuarioId);
    }

    public function crear(int $usuarioId, array $datos)
    {
        $datos['usuario_id'] = $usuarioId;
        $contacto = $this->contactoRepo->guardar($datos);

        $this->bitacoraRepo->registrar([
            'accion'           => 'CREAR_CONTACTO',
            'descripcion'      => "Contacto creado: {$contacto->nombres} {$contacto->apellidos}",
            'target_usuario_id'=> $usuarioId,
            'superusuario_id'  => $usuarioId,
            'created_at'       => now(),
        ]);

        return $contacto;
    }

    public function actualizar(int $id, int $usuarioId, array $datos)
    {
        $contacto = $this->contactoRepo->buscarPorId($id);

        if (!$contacto || $contacto->usuario_id !== $usuarioId || $contacto->eliminado) {
            throw ValidationException::withMessages(['mensaje' => 'Contacto no encontrado o sin permisos.']);
        }

        $resultado = $this->contactoRepo->actualizar($id, $datos);

        $this->bitacoraRepo->registrar([
            'accion'           => 'EDITAR_CONTACTO',
            'descripcion'      => "Contacto editado: {$contacto->nombres} {$contacto->apellidos}",
            'target_usuario_id'=> $usuarioId,
            'superusuario_id'  => $usuarioId,
            'created_at'       => now(),
        ]);

        return $resultado;
    }

    public function eliminar(int $id, int $usuarioId)
    {
        $contacto = $this->contactoRepo->buscarPorId($id);

        if (!$contacto || $contacto->usuario_id !== $usuarioId || $contacto->eliminado) {
            throw ValidationException::withMessages(['mensaje' => 'Contacto no encontrado o sin permisos.']);
        }

        $resultado = $this->contactoRepo->softDelete($id);

        $this->bitacoraRepo->registrar([
            'accion'           => 'ELIMINAR_CONTACTO',
            'descripcion'      => "Contacto eliminado: {$contacto->nombres} {$contacto->apellidos}",
            'target_usuario_id'=> $usuarioId,
            'superusuario_id'  => $usuarioId,
            'created_at'       => now(),
        ]);

        return $resultado;
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
