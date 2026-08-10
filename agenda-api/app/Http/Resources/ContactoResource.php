<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'tipo' => $this->tipo,
            'tipo_label' => $this->tipo,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'sitio_web' => $this->sitio_web,
            'empresa' => $this->empresa,
            'cargo' => $this->cargo,
            'estado' => $this->eliminado ? 'Eliminado' : 'Activo',
        ];
    }

    public function with($request)
    {
        return [
            'exito' => true,
        ];
    }
}
