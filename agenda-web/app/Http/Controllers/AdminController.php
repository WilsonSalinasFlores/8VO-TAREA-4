<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class AdminController extends Controller
{
    private ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function usuarios()
    {
        $res = $this->api->authGet('/admin/usuarios');
        $raw = $res->successful() ? ($res->json('data') ?? []) : [];

        // Mapear campos de la API al formato esperado por la vista
        $usuarios = array_map(function ($u) {
            $u['estado'] = ($u['is_active'] ?? 1) ? 'ACTIVO' : 'INACTIVO';
            $u['correo'] = $u['correo'] ?? $u['email'] ?? '—';
            return $u;
        }, $raw);

        return view('admin.usuarios', compact('usuarios'));
    }

    public function cambiarEstado(Request $request, $id)
    {
        $res = $this->api->authPatch("/admin/usuarios/$id/estado");
        if ($res->successful()) {
            return back()->with('success', 'Estado de usuario actualizado.');
        }
        return back()->with('error', 'Error al cambiar estado.');
    }

    public function agenda($id)
    {
        $res = $this->api->authGet("/admin/usuarios/$id/agenda");
        $data = $res->successful() ? ($res->json('data') ?? []) : [];
        return response()->json($data);
    }

    public function bitacora(Request $request)
    {
        $perPage = in_array((int)$request->query('per_page'), [10, 20, 50, 100])
            ? (int)$request->query('per_page')
            : 10;
        $page    = max(1, (int)$request->query('page', 1));
        $filtros = array_filter([
            'accion'  => $request->query('accion'),
            'usuario' => $request->query('usuario'),
        ]);

        $queryParams = array_merge($filtros, [
            'per_page' => $perPage,
            'page'     => $page,
        ]);

        $res = $this->api->authGet('/admin/bitacora', $queryParams);
        $raw  = $res->successful() ? ($res->json('data')  ?? []) : [];
        $meta = $res->successful() ? ($res->json('meta')  ?? []) : [];

        // Normalizar cada registro
        $bitacora = array_map(function ($b) {
            $b['fecha_hora'] = $b['fecha'] ?? $b['created_at'] ?? null;
            // superusuario ya viene como objeto desde BitacoraResource
            if (!isset($b['usuario'])) {
                $b['usuario'] = $b['superusuario'] ?? ['nombre' => 'Sistema', 'cedula' => ''];
            }
            return $b;
        }, $raw);

        $paginacion = [
            'current_page' => $meta['current_page'] ?? $page,
            'last_page'    => $meta['last_page']    ?? 1,
            'total'        => $meta['total']        ?? count($bitacora),
            'per_page'     => $meta['per_page']     ?? $perPage,
            'from'         => $meta['from']         ?? 1,
            'to'           => $meta['to']           ?? count($bitacora),
        ];

        $acciones = [
            'CAMBIO_ESTADO', 'CREAR_CONTACTO', 'EDITAR_CONTACTO',
            'ELIMINAR_CONTACTO', 'LOGIN', 'REGISTRO',
        ];

        return view('admin.bitacora', compact('bitacora', 'paginacion', 'filtros', 'perPage', 'acciones'));
    }
}
