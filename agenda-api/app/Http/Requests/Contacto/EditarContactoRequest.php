<?php
namespace App\Http\Requests\Contacto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditarContactoRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'nombres' => 'nullable|min:2',
            'apellidos' => 'nullable|min:2',
            'tipo' => 'nullable|in:Trabajo,Personal,Proveedores,Otros',
            'direccion' => 'nullable',
            'telefono' => 'nullable|regex:/^\d{7,15}$/',
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
