<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistroRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'cedula' => 'required|digits:10|unique:usuarios',
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email',
            'password' => 'required|min:8|regex:/^(?=.*[A-Z])(?=.*\d)/',
            'preguntas' => 'required|array|size:3',
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
