<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaRecuperacion extends Model
{
    protected $table = 'preguntas_recuperacion';
    protected $fillable = ['usuario_id', 'catalogo_pregunta_id', 'respuesta'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function catalogoPregunta()
    {
        return $this->belongsTo(CatalogoPregunta::class);
    }
}
