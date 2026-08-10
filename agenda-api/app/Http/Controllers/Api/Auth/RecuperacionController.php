<?php
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
        return response()->json(['exito' => true, 'data' => $preguntas]);
    }

    public function verificar(RecuperacionRequest $request)
    {
        $this->recuperacionService->verificarRespuestas($request->validated());
        return response()->json(['exito' => true, 'mensaje' => 'Respuestas correctas.']);
    }

    public function restablecer(Request $request)
    {
        $this->recuperacionService->restablecerPassword($request->all());
        return response()->json(['exito' => true, 'mensaje' => 'Contraseña restablecida exitosamente.']);
    }
}
