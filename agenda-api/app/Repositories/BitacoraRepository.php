<?php
namespace App\Repositories;

use App\Models\BitacoraAuditoria;
use App\Repositories\Contracts\BitacoraRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BitacoraRepository extends BaseRepository implements BitacoraRepositoryInterface
{
    public function __construct(BitacoraAuditoria $model)
    {
        parent::__construct($model);
    }

    public function guardar(array $datos): Model
    {
        return $this->model->create($datos);
    }

    public function registrar(array $datos)
    {
        return $this->guardar($datos);
    }

    public function listar(array $filtros = [], int $perPage = 10)
    {
        $query = $this->model
            ->with(['superusuario', 'targetUsuario'])
            ->orderBy('created_at', 'desc');

        if (!empty($filtros['accion'])) {
            $query->where('accion', $filtros['accion']);
        }

        if (!empty($filtros['usuario'])) {
            $busqueda = $filtros['usuario'];
            $query->whereHas('superusuario', function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('cedula', 'like', "%{$busqueda}%");
            });
        }

        return $query->paginate($perPage);
    }
}
