<?php
namespace App\Repositories\Contracts;

interface PreguntaRepositoryInterface
{
    public function guardarPreguntas(array $preguntas);
    public function obtenerDeUsuario(int $usuarioId);
    public function verificarRespuestas(int $usuarioId, array $respuestas);
}
