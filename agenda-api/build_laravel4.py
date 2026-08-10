import os

base_path = r"d:\UNIANDES\8VO\HERRAMIENTAS DE DESARROLLO DE SOFTWARE\CLASES\Tarea Semana 4\agenda-api"

files = {
    # CONTROLLERS
    "app/Http/Controllers/Api/Auth/AuthController.php": r"""<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistroRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use App\Http\Resources\UsuarioResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function registro(RegistroRequest $request)
    {
        $usuario = $this->authService->registro($request->validated());
        return response()->json(['usuario' => new UsuarioResource($usuario)], 201);
    }

    public function login(LoginRequest $request)
    {
        $resultado = $this->authService->login($request->validated());
        return response()->json([
            'token' => $resultado['token'],
            'usuario' => new UsuarioResource($resultado['usuario']),
            'primer_login' => $resultado['primer_login']
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return response()->json(['mensaje' => 'Sesión cerrada exitosamente']);
    }
}
""",
    "app/Http/Controllers/Api/Auth/RecuperacionController.php": r"""<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RecuperacionRequest;
use App\Services\Auth\RecuperacionService;
use Illuminate\Http\Request;

class RecuperacionController extends Controller
{
    protected $recuperacionService;

    public function __construct(RecuperacionService $recuperacionService)
    {
        $this->recuperacionService = $recuperacionService;
    }

    public function obtenerPreguntas(Request $request)
    {
        $cedula = $request->query('cedula');
        if (!$cedula) {
            return response()->json(['mensaje' => 'Cédula requerida'], 400);
        }
        $preguntas = $this->recuperacionService->obtenerPreguntas($cedula);
        return response()->json($preguntas);
    }

    public function verificar(RecuperacionRequest $request)
    {
        $this->recuperacionService->verificarRespuestas($request->validated());
        return response()->json(['mensaje' => 'Respuestas correctas.']);
    }

    public function restablecer(Request $request)
    {
        $this->recuperacionService->restablecerPassword($request->all());
        return response()->json(['mensaje' => 'Contraseña restablecida exitosamente.']);
    }
}
""",
    "app/Http/Controllers/Api/Auth/PrimerLoginController.php": r"""<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CambioPasswordRequest;
use App\Services\Auth\PrimerLoginService;
use Illuminate\Http\Request;

class PrimerLoginController extends Controller
{
    protected $primerLoginService;

    public function __construct(PrimerLoginService $primerLoginService)
    {
        $this->primerLoginService = $primerLoginService;
    }

    public function cambiarPassword(CambioPasswordRequest $request)
    {
        $this->primerLoginService->cambiarPassword($request->user(), $request->validated());
        return response()->json(['mensaje' => 'Contraseña actualizada exitosamente.']);
    }

    public function registrarPreguntas(Request $request)
    {
        $request->validate(['preguntas' => 'required|array|size:3']);
        $this->primerLoginService->registrarPreguntas($request->user(), $request->all());
        return response()->json(['mensaje' => 'Preguntas registradas exitosamente.']);
    }
}
""",
    "app/Http/Controllers/Api/ContactoController.php": r"""<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contacto\CrearContactoRequest;
use App\Http\Requests\Contacto\EditarContactoRequest;
use App\Services\ContactoService;
use App\Http\Resources\ContactoResource;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    protected $contactoService;

    public function __construct(ContactoService $contactoService)
    {
        $this->contactoService = $contactoService;
    }

    public function index(Request $request)
    {
        $contactos = $this->contactoService->listar($request->user()->id);
        return ContactoResource::collection($contactos);
    }

    public function store(CrearContactoRequest $request)
    {
        $contacto = $this->contactoService->crear($request->user()->id, $request->validated());
        return new ContactoResource($contacto);
    }

    public function show(Request $request, $id)
    {
        $contacto = $this->contactoService->obtener($id, $request->user()->id);
        return new ContactoResource($contacto);
    }

    public function update(EditarContactoRequest $request, $id)
    {
        $contacto = $this->contactoService->actualizar($id, $request->user()->id, $request->validated());
        return new ContactoResource($contacto);
    }

    public function destroy(Request $request, $id)
    {
        $this->contactoService->eliminar($id, $request->user()->id);
        return response()->json(['mensaje' => 'Contacto eliminado exitosamente']);
    }
}
""",
    "app/Http/Controllers/Api/AdminController.php": r"""<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use App\Http\Resources\UsuarioResource;
use App\Http\Resources\ContactoResource;
use App\Http\Resources\BitacoraResource;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function listarUsuarios()
    {
        $usuarios = $this->adminService->listarUsuarios();
        return UsuarioResource::collection($usuarios);
    }

    public function verAgenda($id)
    {
        $contactos = $this->adminService->verAgenda($id);
        return ContactoResource::collection($contactos);
    }

    public function cambiarEstado($id)
    {
        $estado = $this->adminService->cambiarEstado($id);
        return response()->json(['mensaje' => 'Estado cambiado a ' . ($estado ? 'Activo' : 'Inactivo')]);
    }

    public function bitacora()
    {
        $bitacora = $this->adminService->listarBitacora();
        return BitacoraResource::collection($bitacora);
    }
}
""",
    "app/Http/Controllers/Api/CatalogoController.php": r"""<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogoPregunta;

class CatalogoController extends Controller
{
    public function preguntas()
    {
        return response()->json(CatalogoPregunta::all());
    }
}
""",
    
    # APP SERVICE PROVIDER
    "app/Providers/AppServiceProvider.php": r"""<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\UsuarioRepository;
use App\Repositories\Contracts\ContactoRepositoryInterface;
use App\Repositories\ContactoRepository;
use App\Repositories\Contracts\PreguntaRepositoryInterface;
use App\Repositories\PreguntaRepository;
use App\Repositories\Contracts\BitacoraRepositoryInterface;
use App\Repositories\BitacoraRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(ContactoRepositoryInterface::class, ContactoRepository::class);
        $this->app->bind(PreguntaRepositoryInterface::class, PreguntaRepository::class);
        $this->app->bind(BitacoraRepositoryInterface::class, BitacoraRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
""",

    # API ROUTES
    "routes/api.php": r"""<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RecuperacionController;
use App\Http\Controllers\Api\Auth\PrimerLoginController;
use App\Http\Controllers\Api\ContactoController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CatalogoController;

// Public routes
Route::post('/auth/registro', [AuthController::class, 'registro']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/catalogo/preguntas', [CatalogoController::class, 'preguntas']);
Route::get('/auth/recuperar/preguntas', [RecuperacionController::class, 'obtenerPreguntas']);
Route::post('/auth/recuperar/verificar', [RecuperacionController::class, 'verificar']);
Route::post('/auth/recuperar/restablecer', [RecuperacionController::class, 'restablecer']);

// Authenticated routes
Route::middleware(['auth:sanctum', 'usuario.activo'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::apiResource('contactos', ContactoController::class);
    
    // Superuser routes
    Route::middleware('es.superusuario')->group(function () {
        Route::post('/auth/primer-login', [PrimerLoginController::class, 'cambiarPassword'])->withoutMiddleware('usuario.activo');
        Route::post('/auth/primer-login/preguntas', [PrimerLoginController::class, 'registrarPreguntas'])->withoutMiddleware('usuario.activo');
        
        Route::get('/admin/usuarios', [AdminController::class, 'listarUsuarios']);
        Route::get('/admin/usuarios/{id}/agenda', [AdminController::class, 'verAgenda']);
        Route::patch('/admin/usuarios/{id}/estado', [AdminController::class, 'cambiarEstado']);
        Route::get('/admin/bitacora', [AdminController::class, 'bitacora']);
    });
});
""",

    # BOOTSTRAP APP
    "bootstrap/app.php": r"""<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerificarUsuarioActivo;
use App\Http\Middleware\EsSuperusuario;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'usuario.activo' => VerificarUsuarioActivo::class,
            'es.superusuario' => EsSuperusuario::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
"""
}

for path, content in files.items():
    full_path = os.path.join(base_path, path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, "w", encoding="utf-8") as f:
        f.write(content)
print("Files created.")
