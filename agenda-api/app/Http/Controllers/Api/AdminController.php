<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use App\Http\Resources\UsuarioResource;
use App\Http\Resources\ContactoResource;
use App\Http\Resources\BitacoraResource;
use Illuminate\Http\Request;

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
        return UsuarioResource::collection($usuarios)->additional(['exito' => true]);
    }

    public function verAgenda($id)
    {
        $contactos = $this->adminService->verAgenda($id);
        return ContactoResource::collection($contactos)->additional(['exito' => true]);
    }

    public function cambiarEstado($id)
    {
        $estado = $this->adminService->cambiarEstado($id);
        return response()->json(['exito' => true, 'mensaje' => 'Estado cambiado a ' . ($estado ? 'Activo' : 'Inactivo')]);
    }

    public function bitacora(Request $request)
    {
        $filtros = array_filter([
            'accion'  => $request->query('accion'),
            'usuario' => $request->query('usuario'),
        ]);
        $perPage  = in_array((int)$request->query('per_page'), [10, 20, 50, 100])
            ? (int)$request->query('per_page')
            : 10;

        $bitacora = $this->adminService->listarBitacora($filtros, $perPage);
        return BitacoraResource::collection($bitacora)->additional(['exito' => true]);
    }

    public function sesiones()
    {
        $tokens = $this->adminService->listarSesiones();
        return response()->json(['exito' => true, 'data' => $tokens]);
    }
}
