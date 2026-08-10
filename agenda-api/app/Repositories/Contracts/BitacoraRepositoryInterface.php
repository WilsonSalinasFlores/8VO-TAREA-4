<?php
namespace App\Repositories\Contracts;

interface BitacoraRepositoryInterface
{
    public function registrar(array $datos);
    public function listar();
}
