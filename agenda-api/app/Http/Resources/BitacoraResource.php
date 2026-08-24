<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BitacoraResource extends JsonResource
{
    public function toArray($request)
    {
        $super  = $this->superusuario;
        $target = $this->targetUsuario ?? null;

        return [
            'id'          => $this->id,
            'accion'      => $this->accion,
            'descripcion' => $this->descripcion,
            'superusuario' => $super ? [
                'id'     => $super->id,
                'nombre' => $super->nombre,
                'cedula' => $super->cedula,
            ] : null,
            'target_usuario' => $target ? [
                'id'     => $target->id,
                'nombre' => $target->nombre,
                'cedula' => $target->cedula,
            ] : null,
            'fecha' => $this->created_at
                ? \Carbon\Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
                : null,
        ];
    }
}
