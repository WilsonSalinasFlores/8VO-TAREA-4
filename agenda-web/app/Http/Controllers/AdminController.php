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
        $usuarios = $res->successful() ? ($res->json('data') ?? []) : [];
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

    public function bitacora()
    {
        $res = $this->api->authGet('/admin/bitacora');
        $bitacora = $res->successful() ? ($res->json('data') ?? []) : [];
        return view('admin.bitacora', compact('bitacora'));
    }
}
