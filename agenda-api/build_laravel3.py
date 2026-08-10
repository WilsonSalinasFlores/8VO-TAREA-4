import os

base_path = r"d:\UNIANDES\8VO\HERRAMIENTAS DE DESARROLLO DE SOFTWARE\CLASES\Tarea Semana 4\agenda-api"

files = {
    # SERVICES
    "app/Services/BaseService.php": r"""<?php
namespace App\Services;

abstract class BaseService
{
    // Common service methods if any
}
""",
    "app/Services/Auth/AuthService.php": r"""<?php
namespace App\Services\Auth;

use App\Services\BaseService;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Contracts\PreguntaRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService extends BaseService
{
    protected $usuarioRepo;
    protected $preguntaRepo;

    public function __construct(UsuarioRepositoryInterface $usuarioRepo, PreguntaRepositoryInterface $preguntaRepo)
    {
        $this->usuarioRepo = $usuarioRepo;
        $this->preguntaRepo = $preguntaRepo;
    }

    public function login(array $credenciales)
    {
        $usuario = $this->usuarioRepo->buscarPorCedula($credenciales['cedula']);

        if (!$usuario || !Hash::check($credenciales['password'], $usuario->password)) {
            throw ValidationException::withMessages(['mensaje' => 'Credenciales incorrectas.']);
        }

        if (!$usuario->is_active) {
            throw ValidationException::withMessages(['mensaje' => 'Su cuenta ha sido deshabilitada por el administrador. Contacte a soporte.']);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'usuario' => $usuario,
            'primer_login' => (bool) $usuario->primer_login,
        ];
    }

    public function registro(array $datos)
    {
        $datos['password'] = Hash::make($datos['password']);
        $datos['rol'] = 'usuario';
        
        $usuario = $this->usuarioRepo->guardar($datos);

        $preguntas = array_map(function($p) use ($usuario) {
            return [
                'usuario_id' => $usuario->id,
                'catalogo_pregunta_id' => $p['id'],
                'respuesta' => strtolower(trim($p['respuesta'])),
            ];
        }, $datos['preguntas']);

        $this->preguntaRepo->guardarPreguntas($preguntas);

        return $usuario;
    }

    public function logout($usuario)
    {
        $usuario->currentAccessToken()->delete();
    }
}
""",
    "app/Services/Auth/RecuperacionService.php": r"""<?php
namespace App\Services\Auth;

use App\Services\BaseService;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Contracts\PreguntaRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RecuperacionService extends BaseService
{
    protected $usuarioRepo;
    protected $preguntaRepo;

    public function __construct(UsuarioRepositoryInterface $usuarioRepo, PreguntaRepositoryInterface $preguntaRepo)
    {
        $this->usuarioRepo = $usuarioRepo;
        $this->preguntaRepo = $preguntaRepo;
    }

    public function obtenerPreguntas(string $cedula)
    {
        $usuario = $this->usuarioRepo->buscarPorCedula($cedula);
        if (!$usuario) {
            throw ValidationException::withMessages(['mensaje' => 'Usuario no encontrado.']);
        }

        return $this->preguntaRepo->obtenerDeUsuario($usuario->id);
    }

    public function verificarRespuestas(array $datos)
    {
        $usuario = $this->usuarioRepo->buscarPorCedula($datos['cedula']);
        if (!$usuario) {
            throw ValidationException::withMessages(['mensaje' => 'Usuario no encontrado.']);
        }

        if (!$this->preguntaRepo->verificarRespuestas($usuario->id, $datos['respuestas'])) {
            throw ValidationException::withMessages(['mensaje' => 'Respuestas incorrectas.']);
        }

        return $usuario;
    }

    public function restablecerPassword(array $datos)
    {
        $usuario = $this->verificarRespuestas($datos);
        
        if (!isset($datos['password_nuevo']) || strlen($datos['password_nuevo']) < 8) {
            throw ValidationException::withMessages(['mensaje' => 'El nuevo password no es válido.']);
        }

        $this->usuarioRepo->actualizar($usuario->id, [
            'password' => Hash::make($datos['password_nuevo'])
        ]);

        return true;
    }
}
""",
    "app/Services/Auth/PrimerLoginService.php": r"""<?php
namespace App\Services\Auth;

use App\Services\BaseService;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Contracts\PreguntaRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PrimerLoginService extends BaseService
{
    protected $usuarioRepo;
    protected $preguntaRepo;

    public function __construct(UsuarioRepositoryInterface $usuarioRepo, PreguntaRepositoryInterface $preguntaRepo)
    {
        $this->usuarioRepo = $usuarioRepo;
        $this->preguntaRepo = $preguntaRepo;
    }

    public function cambiarPassword($usuario, array $datos)
    {
        if (!Hash::check($datos['password_actual'], $usuario->password)) {
            throw ValidationException::withMessages(['mensaje' => 'Contraseña actual incorrecta.']);
        }

        $this->usuarioRepo->actualizar($usuario->id, [
            'password' => Hash::make($datos['password_nuevo']),
            'primer_login' => 0
        ]);
    }

    public function registrarPreguntas($usuario, array $datos)
    {
        $preguntas = array_map(function($p) use ($usuario) {
            return [
                'usuario_id' => $usuario->id,
                'catalogo_pregunta_id' => $p['id'],
                'respuesta' => strtolower(trim($p['respuesta'])),
            ];
        }, $datos['preguntas']);

        $this->preguntaRepo->guardarPreguntas($preguntas);
    }
}
""",
    "app/Services/ContactoService.php": r"""<?php
namespace App\Services;

use App\Repositories\Contracts\ContactoRepositoryInterface;
use Illuminate\Validation\ValidationException;

class ContactoService extends BaseService
{
    protected $contactoRepo;

    public function __construct(ContactoRepositoryInterface $contactoRepo)
    {
        $this->contactoRepo = $contactoRepo;
    }

    public function listar(int $usuarioId)
    {
        return $this->contactoRepo->obtenerActivos($usuarioId);
    }

    public function crear(int $usuarioId, array $datos)
    {
        $datos['usuario_id'] = $usuarioId;
        return $this->contactoRepo->guardar($datos);
    }

    public function actualizar(int $id, int $usuarioId, array $datos)
    {
        $contacto = $this->contactoRepo->buscarPorId($id);
        
        if (!$contacto || $contacto->usuario_id !== $usuarioId || $contacto->eliminado) {
            throw ValidationException::withMessages(['mensaje' => 'Contacto no encontrado o sin permisos.']);
        }

        return $this->contactoRepo->actualizar($id, $datos);
    }

    public function eliminar(int $id, int $usuarioId)
    {
        $contacto = $this->contactoRepo->buscarPorId($id);
        
        if (!$contacto || $contacto->usuario_id !== $usuarioId || $contacto->eliminado) {
            throw ValidationException::withMessages(['mensaje' => 'Contacto no encontrado o sin permisos.']);
        }

        return $this->contactoRepo->softDelete($id);
    }

    public function obtener(int $id, int $usuarioId)
    {
        $contacto = $this->contactoRepo->buscarPorId($id);
        
        if (!$contacto || $contacto->usuario_id !== $usuarioId || $contacto->eliminado) {
            throw ValidationException::withMessages(['mensaje' => 'Contacto no encontrado o sin permisos.']);
        }

        return $contacto;
    }
}
""",
    "app/Services/AdminService.php": r"""<?php
namespace App\Services;

use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Contracts\ContactoRepositoryInterface;
use App\Repositories\Contracts\BitacoraRepositoryInterface;
use Illuminate\Validation\ValidationException;

class AdminService extends BaseService
{
    protected $usuarioRepo;
    protected $contactoRepo;
    protected $bitacoraRepo;

    public function __construct(
        UsuarioRepositoryInterface $usuarioRepo,
        ContactoRepositoryInterface $contactoRepo,
        BitacoraRepositoryInterface $bitacoraRepo
    ) {
        $this->usuarioRepo = $usuarioRepo;
        $this->contactoRepo = $contactoRepo;
        $this->bitacoraRepo = $bitacoraRepo;
    }

    public function listarUsuarios()
    {
        return $this->usuarioRepo->listarTodos();
    }

    public function verAgenda(int $usuarioId)
    {
        return $this->contactoRepo->obtenerTodosDeUsuario($usuarioId);
    }

    public function cambiarEstado(int $usuarioId)
    {
        $usuario = $this->usuarioRepo->buscarPorId($usuarioId);
        if (!$usuario || $usuario->rol === 'superusuario') {
            throw ValidationException::withMessages(['mensaje' => 'Usuario inválido.']);
        }

        $nuevoEstado = !$usuario->is_active;
        $this->usuarioRepo->cambiarEstado($usuarioId, $nuevoEstado);

        $this->bitacoraRepo->registrar([
            'accion' => 'CAMBIO_ESTADO',
            'descripcion' => "Estado cambiado a " . ($nuevoEstado ? 'Activo' : 'Inactivo'),
            'target_usuario_id' => $usuarioId,
            'superusuario_id' => auth()->id(),
        ]);

        return $nuevoEstado;
    }

    public function listarBitacora()
    {
        return $this->bitacoraRepo->listar();
    }
}
""",
    # RESOURCES
    "app/Http/Resources/UsuarioResource.php": r"""<?php
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
}
""",
    "app/Http/Resources/ContactoResource.php": r"""<?php
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
}
""",
    "app/Http/Resources/BitacoraResource.php": r"""<?php
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
}
"""
}

for path, content in files.items():
    full_path = os.path.join(base_path, path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, "w", encoding="utf-8") as f:
        f.write(content)
print("Files created.")
