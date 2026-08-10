<?php
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
        return response()->json(['exito' => true, 'mensaje' => 'Contraseña actualizada exitosamente.']);
    }

    public function registrarPreguntas(Request $request)
    {
        $request->validate(['preguntas' => 'required|array|size:3']);
        $this->primerLoginService->registrarPreguntas($request->user(), $request->all());
        return response()->json(['exito' => true, 'mensaje' => 'Preguntas registradas exitosamente.']);
    }
}
