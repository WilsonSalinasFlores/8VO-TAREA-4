<?php
namespace App\Repositories\Contracts;

interface UsuarioRepositoryInterface
{
    public function buscarPorCedula(string $cedula);
    public function buscarPorId(int $id);
    public function guardar(array $datos);
    public function actualizar(int $id, array $datos);
    public function listarTodos();
    public function cambiarEstado(int $id, bool $estado);
}
