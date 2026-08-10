<?php
namespace App\Repositories;

use App\Models\Contacto;
use App\Repositories\Contracts\ContactoRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ContactoRepository extends BaseRepository implements ContactoRepositoryInterface
{
    public function __construct(Contacto $model)
    {
        parent::__construct($model);
    }

    public function obtenerActivos(int $usuarioId)
    {
        return $this->model->where('usuario_id', $usuarioId)->where('eliminado', 0)->get();
    }

    public function guardar(array $datos): Model
    {
        return $this->model->create($datos);
    }

    public function actualizar(int $id, array $datos)
    {
        $contacto = $this->buscarPorId($id);
        if ($contacto) {
            $contacto->update($datos);
            return $contacto;
        }
        return null;
    }

    public function softDelete(int $id)
    {
        return $this->actualizar($id, ['eliminado' => 1]);
    }

    public function obtenerTodosDeUsuario(int $usuarioId)
    {
        return $this->model->where('usuario_id', $usuarioId)->get();
    }
}
