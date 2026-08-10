<?php
namespace App\Repositories;

use App\Models\PreguntaRecuperacion;
use App\Repositories\Contracts\PreguntaRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class PreguntaRepository extends BaseRepository implements PreguntaRepositoryInterface
{
    public function __construct(PreguntaRecuperacion $model)
    {
        parent::__construct($model);
    }

    public function guardar(array $datos): Model
    {
        return $this->model->create($datos);
    }

    public function guardarPreguntas(array $preguntas)
    {
        foreach ($preguntas as $pregunta) {
            $this->guardar($pregunta);
        }
    }

    public function obtenerDeUsuario(int $usuarioId)
    {
        return $this->model->where('usuario_id', $usuarioId)->with('catalogoPregunta')->get();
    }

    public function verificarRespuestas(int $usuarioId, array $respuestas)
    {
        // $respuestas is an array of [id => respuesta_texto]
        $validas = 0;
        foreach ($respuestas as $id => $resp) {
            $existe = $this->model->where('usuario_id', $usuarioId)
                ->where('catalogo_pregunta_id', $id)
                ->where('respuesta', strtolower(trim($resp)))
                ->exists();
            if ($existe) $validas++;
        }
        return $validas === 3;
    }
}
