import os

base_path = r"d:\UNIANDES\8VO\HERRAMIENTAS DE DESARROLLO DE SOFTWARE\CLASES\Tarea Semana 4\agenda-api"

files = {
    # SEEDERS
    "database/seeders/SuperusuarioSeeder.php": r"""<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperusuarioSeeder extends Seeder
{
    public function run()
    {
        DB::table('usuarios')->insert([
            'cedula' => 'admin',
            'nombre' => 'Administrador',
            'correo' => 'admin@agenda.com',
            'password' => Hash::make('admin'),
            'rol' => 'superusuario',
            'is_active' => 1,
            'primer_login' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
""",
    "database/seeders/CatalogoPreguntasSeeder.php": r"""<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoPreguntasSeeder extends Seeder
{
    public function run()
    {
        $preguntas = [
            '¿Cuál es el nombre de tu primera mascota?',
            '¿En qué ciudad naciste?',
            '¿Cuál es el apellido de soltera de tu madre?',
            '¿Cuál fue el nombre de tu primera escuela?',
            '¿Cuál es tu comida favorita?',
            '¿Cuál es el nombre de tu mejor amigo de la infancia?',
            '¿Cuál es tu película favorita?',
            '¿Cuál fue el modelo de tu primer vehículo?',
            '¿Cuál es el nombre de tu libro favorito?',
            '¿Cuál es tu equipo deportivo favorito?',
        ];

        foreach ($preguntas as $pregunta) {
            DB::table('catalogo_preguntas')->insert([
                'pregunta' => $pregunta,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
""",
    "database/seeders/DatabaseSeeder.php": r"""<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            SuperusuarioSeeder::class,
            CatalogoPreguntasSeeder::class,
        ]);
    }
}
""",

    # MODELS
    "app/Models/Usuario.php": r"""<?php
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
""",
    "app/Models/Contacto.php": r"""<?php
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
""",
    "app/Models/CatalogoPregunta.php": r"""<?php
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
""",
    "app/Models/PreguntaRecuperacion.php": r"""<?php
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
""",
    "app/Models/BitacoraAuditoria.php": r"""<?php
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
""",

    # REPOSITORIES - CONTRACTS
    "app/Repositories/Contracts/UsuarioRepositoryInterface.php": r"""<?php
namespace App\Repositories\Contracts;

interface UsuarioRepositoryInterface
{
    public function buscarPorCedula(string $cedula);
    public function buscarPorId(int $id);
    public function guardar(array $datos);
    public function actualizar(int $id, array $datos);
    public function listarTodos();
    public function cambiarEstado(int $id, bool $estado);
}
""",
    "app/Repositories/Contracts/ContactoRepositoryInterface.php": r"""<?php
namespace App\Repositories\Contracts;

interface ContactoRepositoryInterface
{
    public function obtenerActivos(int $usuarioId);
    public function guardar(array $datos);
    public function actualizar(int $id, array $datos);
    public function softDelete(int $id);
    public function buscarPorId(int $id);
    public function obtenerTodosDeUsuario(int $usuarioId);
}
""",
    "app/Repositories/Contracts/PreguntaRepositoryInterface.php": r"""<?php
namespace App\Repositories\Contracts;

interface PreguntaRepositoryInterface
{
    public function guardarPreguntas(array $preguntas);
    public function obtenerDeUsuario(int $usuarioId);
    public function verificarRespuestas(int $usuarioId, array $respuestas);
}
""",
    "app/Repositories/Contracts/BitacoraRepositoryInterface.php": r"""<?php
namespace App\Repositories\Contracts;

interface BitacoraRepositoryInterface
{
    public function registrar(array $datos);
    public function listar();
}
""",

    # REPOSITORIES - BASE & IMPL
    "app/Repositories/BaseRepository.php": r"""<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function buscarPorId(int $id): ?Model
    {
        return $this->model->find($id);
    }

    abstract public function guardar(array $datos): Model;
}
""",
    "app/Repositories/UsuarioRepository.php": r"""<?php
namespace App\Repositories;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UsuarioRepository extends BaseRepository implements UsuarioRepositoryInterface
{
    public function __construct(Usuario $model)
    {
        parent::__construct($model);
    }

    public function buscarPorCedula(string $cedula)
    {
        return $this->model->where('cedula', $cedula)->first();
    }

    public function guardar(array $datos): Model
    {
        return $this->model->create($datos);
    }

    public function actualizar(int $id, array $datos)
    {
        $usuario = $this->buscarPorId($id);
        if ($usuario) {
            $usuario->update($datos);
            return $usuario;
        }
        return null;
    }

    public function listarTodos()
    {
        return $this->model->where('rol', '!=', 'superusuario')->get();
    }

    public function cambiarEstado(int $id, bool $estado)
    {
        return $this->actualizar($id, ['is_active' => $estado]);
    }
}
""",
    "app/Repositories/ContactoRepository.php": r"""<?php
namespace App\Repositories;

use App\Models\Contacto;
use App\Repositories\Contracts\ContactoRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ContactoRepository extends BaseRepository implements ContactoRepositoryInterface
{
    public function __construct(Contacto $model)
    {
        parent::__construct($model);
    }

    public function obtenerActivos(int $usuarioId)
    {
        return $this->model->where('usuario_id', $usuarioId)->where('eliminado', 0)->get();
    }

    public function guardar(array $datos): Model
    {
        return $this->model->create($datos);
    }

    public function actualizar(int $id, array $datos)
    {
        $contacto = $this->buscarPorId($id);
        if ($contacto) {
            $contacto->update($datos);
            return $contacto;
        }
        return null;
    }

    public function softDelete(int $id)
    {
        return $this->actualizar($id, ['eliminado' => 1]);
    }

    public function obtenerTodosDeUsuario(int $usuarioId)
    {
        return $this->model->where('usuario_id', $usuarioId)->get();
    }
}
""",
    "app/Repositories/PreguntaRepository.php": r"""<?php
namespace App\Repositories;

use App\Models\PreguntaRecuperacion;
use App\Repositories\Contracts\PreguntaRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class PreguntaRepository extends BaseRepository implements PreguntaRepositoryInterface
{
    public function __construct(PreguntaRecuperacion $model)
    {
        parent::__construct($model);
    }

    public function guardar(array $datos): Model
    {
        return $this->model->create($datos);
    }

    public function guardarPreguntas(array $preguntas)
    {
        foreach ($preguntas as $pregunta) {
            $this->guardar($pregunta);
        }
    }

    public function obtenerDeUsuario(int $usuarioId)
    {
        return $this->model->where('usuario_id', $usuarioId)->with('catalogoPregunta')->get();
    }

    public function verificarRespuestas(int $usuarioId, array $respuestas)
    {
        // $respuestas is an array of [id => respuesta_texto]
        $validas = 0;
        foreach ($respuestas as $id => $resp) {
            $existe = $this->model->where('usuario_id', $usuarioId)
                ->where('catalogo_pregunta_id', $id)
                ->where('respuesta', strtolower(trim($resp)))
                ->exists();
            if ($existe) $validas++;
        }
        return $validas === 3;
    }
}
""",
    "app/Repositories/BitacoraRepository.php": r"""<?php
namespace App\Repositories;

use App\Models\BitacoraAuditoria;
use App\Repositories\Contracts\BitacoraRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BitacoraRepository extends BaseRepository implements BitacoraRepositoryInterface
{
    public function __construct(BitacoraAuditoria $model)
    {
        parent::__construct($model);
    }

    public function guardar(array $datos): Model
    {
        return $this->model->create($datos);
    }

    public function registrar(array $datos)
    {
        return $this->guardar($datos);
    }

    public function listar()
    {
        return $this->model->with(['superusuario', 'targetUsuario'])->orderBy('created_at', 'desc')->get();
    }
}
"""
}

for path, content in files.items():
    full_path = os.path.join(base_path, path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, "w", encoding="utf-8") as f:
        f.write(content)
print("Files created.")
