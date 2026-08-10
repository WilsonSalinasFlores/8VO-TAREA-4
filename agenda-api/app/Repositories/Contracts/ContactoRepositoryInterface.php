<?php
namespace App\Repositories\Contracts;

interface ContactoRepositoryInterface
{
    public function obtenerActivos(int $usuarioId);
    public function guardar(array $datos);
    public function actualizar(int $id, array $datos);
    public function softDelete(int $id);
    public function buscarPorId(int $id);
    public function obtenerTodosDeUsuario(int $usuarioId);
}
