<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BitacoraResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'accion' => $this->accion,
            'descripcion' => $this->descripcion,
            'target_usuario' => $this->target_usuario_id,
            'superusuario' => $this->superusuario_id,
            'fecha' => $this->created_at ? \Carbon\Carbon::parse($this->created_at)->format('Y-m-d H:i:s') : null,
        ];
    }

    public function with($request)
    {
        return [
            'exito' => true,
        ];
    }
}
