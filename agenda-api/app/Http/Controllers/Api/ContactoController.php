<?php
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
        return ContactoResource::collection($contactos)->additional(['exito' => true]);
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
        return response()->json(['exito' => true, 'mensaje' => 'Contacto eliminado exitosamente']);
    }
}
