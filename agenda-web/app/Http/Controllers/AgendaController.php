<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class AgendaController extends Controller
{
    private ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $res = $this->api->authGet('/contactos');
        $contactos = $res->successful() ? ($res->json('data') ?? []) : [];

        $q = $request->query('q');
        if ($q) {
            $q_lower = strtolower($q);
            $contactos = array_filter($contactos, function($c) use ($q_lower) {
                $fields = [$c['nombres'], $c['apellidos'], $c['telefono'], $c['empresa'] ?? '', $c['direccion'] ?? '', $c['tipo'] ?? ''];
                return collect($fields)->contains(fn($f) => str_contains(strtolower((string)$f), $q_lower));
            });
        }

        return view('agenda.lista', compact('contactos', 'q'));
    }

    public function create()
    {
        return view('agenda.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'telefono' => 'required',
            'tipo' => 'required',
        ]);

        $res = $this->api->authPost('/contactos', $request->except('_token'));
        if ($res->successful()) {
            return redirect('/agenda')->with('success', 'Contacto creado exitosamente.');
        }
        $errores = $res->json('errores') ?? [];
        return back()->withInput()->with('error', $res->json('mensaje') ?? 'Error al crear contacto.')->with('errores', $errores);
    }

    public function edit($id)
    {
        $res = $this->api->authGet("/contactos/$id");
        if ($res->successful()) {
            $contacto = $res->json('data') ?? $res->json();
            return view('agenda.editar', compact('contacto'));
        }
        return redirect('/agenda')->with('error', 'Contacto no encontrado.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'telefono' => 'required',
            'tipo' => 'required',
        ]);

        $res = $this->api->authPut("/contactos/$id", $request->except('_token', '_method'));
        if ($res->successful()) {
            return redirect('/agenda')->with('success', 'Contacto actualizado exitosamente.');
        }
        return back()->withInput()->with('error', $res->json('mensaje') ?? 'Error al actualizar contacto.');
    }

    public function destroy($id)
    {
        $res = $this->api->authDelete("/contactos/$id");
        if ($res->successful()) {
            return redirect('/agenda')->with('success', 'Contacto eliminado exitosamente.');
        }
        return back()->with('error', 'Error al eliminar contacto.');
    }
}
