<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['cedula', 'nombre', 'correo', 'password', 'rol', 'is_active', 'primer_login'];
    protected $hidden = ['password'];

    public function contactos()
    {
        return $this->hasMany(Contacto::class);
    }

    public function preguntasRecuperacion()
    {
        return $this->hasMany(PreguntaRecuperacion::class);
    }
}
