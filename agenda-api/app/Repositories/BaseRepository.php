<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function buscarPorId(int $id): ?Model
    {
        return $this->model->find($id);
    }

    abstract public function guardar(array $datos): Model;
}
