<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraAuditoria extends Model
{
    protected $table = 'bitacora_auditoria';
    public $timestamps = false;
    protected $fillable = ['accion', 'descripcion', 'target_usuario_id', 'superusuario_id', 'created_at'];

    public function superusuario()
    {
        return $this->belongsTo(Usuario::class, 'superusuario_id');
    }

    public function targetUsuario()
    {
        return $this->belongsTo(Usuario::class, 'target_usuario_id');
    }
}
