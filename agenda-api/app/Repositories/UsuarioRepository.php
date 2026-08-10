<?php
namespace App\Repositories;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UsuarioRepository extends BaseRepository implements UsuarioRepositoryInterface
{
    public function __construct(Usuario $model)
    {
        parent::__construct($model);
    }

    public function buscarPorCedula(string $cedula)
    {
        return $this->model->where('cedula', $cedula)->first();
    }

    public function guardar(array $datos): Model
    {
        return $this->model->create($datos);
    }

    public function actualizar(int $id, array $datos)
    {
        $usuario = $this->buscarPorId($id);
        if ($usuario) {
            $usuario->update($datos);
            return $usuario;
        }
        return null;
    }

    public function listarTodos()
    {
        return $this->model->where('rol', '!=', 'superusuario')->get();
    }

    public function cambiarEstado(int $id, bool $estado)
    {
        return $this->actualizar($id, ['is_active' => $estado]);
    }
}
