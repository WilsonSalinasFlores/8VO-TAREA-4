<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoPregunta extends Model
{
    protected $table = 'catalogo_preguntas';
    protected $fillable = ['pregunta'];

    public function preguntasRecuperacion()
    {
        return $this->hasMany(PreguntaRecuperacion::class);
    }
}
