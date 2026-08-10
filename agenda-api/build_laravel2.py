import os

base_path = r"d:\UNIANDES\8VO\HERRAMIENTAS DE DESARROLLO DE SOFTWARE\CLASES\Tarea Semana 4\agenda-api"

files = {
    # MIDDLEWARE
    "app/Http/Middleware/VerificarUsuarioActivo.php": r"""<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarUsuarioActivo
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_active) {
            return response()->json(['mensaje' => 'Su cuenta ha sido deshabilitada por el administrador. Contacte a soporte.'], 401);
        }
        return $next($request);
    }
}
""",
    "app/Http/Middleware/EsSuperusuario.php": r"""<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EsSuperusuario
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->rol !== 'superusuario') {
            return response()->json(['mensaje' => 'Acceso no autorizado.'], 403);
        }
        return $next($request);
    }
}
""",

    # REQUESTS
    "app/Http/Requests/Auth/RegistroRequest.php": r"""<?php
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
            'nombre' => 'required|alpha',
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
""",
    "app/Http/Requests/Auth/LoginRequest.php": r"""<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'cedula' => 'required',
            'password' => 'required',
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
""",
    "app/Http/Requests/Auth/CambioPasswordRequest.php": r"""<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CambioPasswordRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'password_actual' => 'required',
            'password_nuevo' => 'required|min:8|regex:/^(?=.*[A-Z])(?=.*\d)/',
            'password_confirmacion' => 'required|same:password_nuevo',
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
""",
    "app/Http/Requests/Auth/RecuperacionRequest.php": r"""<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RecuperacionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'cedula' => 'required|digits:10',
            'respuestas' => 'required|array|size:3',
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
""",
    "app/Http/Requests/Contacto/CrearContactoRequest.php": r"""<?php
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
            'telefono' => 'required|regex:/^\d{7,15}$/',
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
""",
    "app/Http/Requests/Contacto/EditarContactoRequest.php": r"""<?php
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
"""
}

for path, content in files.items():
    full_path = os.path.join(base_path, path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, "w", encoding="utf-8") as f:
        f.write(content)
print("Files created.")
