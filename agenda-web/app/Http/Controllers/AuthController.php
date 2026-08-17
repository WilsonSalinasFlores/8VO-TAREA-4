<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    private ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'cedula' => 'required',
            'password' => 'required',
        ]);

        $response = $this->api->post('/auth/login', $request->only('cedula', 'password'));

        if ($response->successful()) {
            $data    = $response->json('data');
            $usuario = $data['user'];
            session([
                'token'   => $data['token'],
                'usuario' => $usuario,
                'rol'     => $usuario['rol'],
            ]);

            if (!empty($data['primer_login'])) {
                return redirect('/primer-login');
            }

            return $usuario['rol'] === 'superusuario'
                ? redirect('/admin/usuarios')
                : redirect('/agenda');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Credenciales incorrectas.');
    }

    public function registroForm()
    {
        $res = $this->api->authGet('/catalogo/preguntas');
        $preguntas = $res->successful() ? ($res->json('data') ?? $res->json()) : [];
        return view('auth.registro', compact('preguntas'));
    }

    public function registro(Request $request)
    {
        $request->validate([
            'cedula' => 'required|digits:10',
            'nombre' => 'required',
            'correo' => 'required|email',
            'password' => 'required|min:6',
            'respuestas' => 'required|array|size:3',
        ]);

        $response = $this->api->post('/auth/registro', $request->all());

        if ($response->successful()) {
            return redirect('/login')->with('success', 'Registro exitoso. Puede iniciar sesión.');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Error al registrar.');
    }

    public function recuperarForm()
    {
        return view('auth.recuperar');
    }

    public function recuperar(Request $request)
    {
        $step = $request->input('step', 1);

        if ($step == 1) {
            $request->validate(['cedula' => 'required']);
            $res = $this->api->post('/auth/recuperar/preguntas', ['cedula' => $request->cedula]);
            if ($res->successful()) {
                session(['recuperar_cedula' => $request->cedula, 'recuperar_preguntas' => $res->json('data') ?? $res->json()]);
                return back()->with('step', 2);
            }
            return back()->with('error', 'Cédula no encontrada.');
        }

        if ($step == 2) {
            $request->validate(['respuestas' => 'required|array']);
            $cedula = session('recuperar_cedula');
            $res = $this->api->post('/auth/recuperar/verificar', ['cedula' => $cedula, 'respuestas' => $request->respuestas]);
            if ($res->successful()) {
                return back()->with('step', 3);
            }
            return back()->with('error', 'Respuestas incorrectas.')->with('step', 2);
        }

        if ($step == 3) {
            $request->validate(['password' => 'required|confirmed|min:6']);
            $cedula = session('recuperar_cedula');
            $res = $this->api->post('/auth/recuperar/restablecer', [
                'cedula'   => $cedula,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);
            if ($res->successful()) {
                session()->forget(['recuperar_cedula', 'recuperar_preguntas']);
                return redirect('/login')->with('success', 'Contraseña actualizada correctamente.');
            }
            return back()->with('error', 'Error al actualizar contraseña.')->with('step', 3);
        }
    }

    public function primerLoginForm()
    {
        $res = $this->api->authGet('/catalogo/preguntas');
        $preguntas = $res->successful() ? ($res->json('data') ?? $res->json()) : [];
        return view('auth.primer-login', compact('preguntas'));
    }

    public function primerLogin(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
            'respuestas' => 'required|array|size:3'
        ]);

        $res1 = $this->api->authPost('/auth/primer-login', [
            'password_actual'       => $request->current_password,
            'password_nuevo'        => $request->password,
            'password_confirmacion' => $request->password_confirmation,
        ]);

        if (!$res1->successful()) {
            return back()->with('error', $res1->json('mensaje') ?? 'Error al cambiar contraseña.');
        }

        $res2 = $this->api->authPost('/auth/primer-login/preguntas', [
            'preguntas' => collect($request->respuestas)->map(fn($r, $i) => [
                'id'        => $r['id_pregunta'],
                'respuesta' => $r['respuesta'],
            ])->values()->all(),
        ]);

        if (!$res2->successful()) {
            return back()->with('error', 'Contraseña cambiada pero error al guardar preguntas.');
        }

        $user = session('usuario');
        $user['estado'] = 'ACTIVO';
        session(['usuario' => $user]);

        if ($user['rol'] === 'superusuario') {
            return redirect('/admin/usuarios')->with('success', 'Configuración inicial completada.');
        }
        return redirect('/agenda')->with('success', 'Configuración inicial completada.');
    }

    public function logout()
    {
        $this->api->authPost('/auth/logout', []);
        session()->flush();
        return redirect('/login');
    }
}
