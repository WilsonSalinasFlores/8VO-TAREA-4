<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $fillable = ['usuario_id', 'nombres', 'apellidos', 'tipo', 'direccion', 'telefono', 'sitio_web', 'empresa', 'cargo', 'eliminado'];
    
    protected $casts = [
        'eliminado' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
