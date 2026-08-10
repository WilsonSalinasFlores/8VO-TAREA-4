<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'cedula' => $this->cedula,
            'nombre' => $this->nombre,
            'correo' => $this->correo,
            'rol' => $this->rol,
            'is_active' => (bool) $this->is_active,
            'primer_login' => (bool) $this->primer_login,
        ];
    }

    public function with($request)
    {
        return [
            'exito' => true,
        ];
    }
}
