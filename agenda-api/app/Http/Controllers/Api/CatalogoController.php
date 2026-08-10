<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogoPregunta;

class CatalogoController extends Controller
{
    public function preguntas()
    {
        return response()->json([
            'exito' => true,
            'data' => CatalogoPregunta::all()
        ]);
    }
}
