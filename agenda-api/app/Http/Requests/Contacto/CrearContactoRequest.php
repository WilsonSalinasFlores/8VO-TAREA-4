<?php
namespace App\Http\Requests\Contacto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CrearContactoRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'nombres' => 'required|min:2',
            'apellidos' => 'required|min:2',
            'tipo' => 'required|in:Trabajo,Personal,Proveedores,Otros',
            'direccion' => 'required',
            'telefono' => 'nullable|string',
            'telefonos' => 'required|array|min:1',
            'telefonos.*.numero' => 'required|string',
            'telefonos.*.tipo' => 'required|string|in:Celular,Trabajo,Casa,Principal,Otros',
            'sitio_web' => 'nullable|url',
            'empresa' => 'nullable|string',
            'cargo' => 'nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'mensaje' => 'Error de validación',
            'errores' => $validator->errors()
        ], 422));
    }
}
