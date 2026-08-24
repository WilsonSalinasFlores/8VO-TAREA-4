<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    private ApiService $api;
    private const REMEMBER_COOKIE = 'agenda_remember';
    private const REMEMBER_DAYS   = 30;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    // ──────────────────────────────────────────────
    // LOGIN
    // ──────────────────────────────────────────────

    public function loginForm()
    {
        // Si ya hay sesión activa, redirigir
        if (session('token')) {
            return session('rol') === 'superusuario'
                ? redirect('/admin/usuarios')
                : redirect('/agenda');
        }

        // Autocompletar cédula si existe cookie "Recordarme"
        $remembered = null;
        if (request()->hasCookie(self::REMEMBER_COOKIE)) {
            $payload = json_decode(decrypt(request()->cookie(self::REMEMBER_COOKIE)), true);
            $remembered = $payload['cedula'] ?? null;
        }

        return view('auth.login', compact('remembered'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'cedula'   => 'required',
            'password' => 'required',
        ]);

        $response = $this->api->post('/auth/login', $request->only('cedula', 'password'));

        if ($response->successful()) {
            $data    = $response->json('data');
            $usuario = $data['user'];

            session([
                'token'      => $data['token'],
                'usuario'    => $usuario,
                'rol'        => $usuario['rol'],
                'login_time' => now()->toDateTimeString(),
                'login_ip'   => $request->ip(),
                'login_ua'   => $request->userAgent(),
            ]);

            // "Recordarme" — encolar cookie httpOnly cifrada por 30 días
            if ($request->boolean('remember')) {
                Cookie::queue(
                    self::REMEMBER_COOKIE,
                    encrypt(json_encode(['cedula' => $request->cedula])),
                    self::REMEMBER_DAYS * 24 * 60,  // minutos
                    '/',
                    null,
                    false,  // secure: false en localhost (true en producción)
                    true,   // httpOnly
                    false,
                    'Strict'
                );
            } else {
                // Si desmarca "Recordarme", borrar cookie existente
                Cookie::queue(Cookie::forget(self::REMEMBER_COOKIE));
            }

            if (!empty($data['primer_login'])) {
                return redirect('/primer-login');
            }

            return $usuario['rol'] === 'superusuario'
                ? redirect('/admin/usuarios')
                : redirect('/agenda');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Credenciales incorrectas.');
    }

    // ──────────────────────────────────────────────
    // REGISTRO
    // ──────────────────────────────────────────────

    public function registroForm()
    {
        $res       = $this->api->authGet('/catalogo/preguntas');
        $preguntas = $res->successful() ? ($res->json('data') ?? $res->json()) : [];
        return view('auth.registro', compact('preguntas'));
    }

    public function registro(Request $request)
    {
        $request->validate([
            'cedula'    => 'required|digits:10',
            'nombre'    => 'required',
            'correo'    => 'required|email',
            'password'  => 'required|min:8|regex:/^(?=.*[A-Z])(?=.*\d)/|confirmed',
            'respuestas'=> 'required|array|size:3',
        ], [
            'password.min'   => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe incluir al menos una letra mayúscula y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);

        $payload = $request->only('cedula', 'nombre', 'correo', 'password');
        
        // Mapear "respuestas" del frontend al formato "preguntas" que espera la API
        $payload['preguntas'] = collect($request->respuestas)->map(function ($r) {
            return [
                'id'        => $r['pregunta_id'],
                'respuesta' => $r['respuesta'],
            ];
        })->values()->toArray();

        $response = $this->api->post('/auth/registro', $payload);

        if ($response->successful()) {
            return redirect('/login')->with('success', '¡Cuenta creada exitosamente! Ya puede iniciar sesión.');
        }

        $errores = $response->json('errores');
        if ($errores) {
            return back()->withErrors($errores)->withInput();
        }

        return back()->with('error', $response->json('mensaje') ?? 'Error al registrar.')->withInput();
    }

    // ──────────────────────────────────────────────
    // RECUPERAR CONTRASEÑA
    // ──────────────────────────────────────────────

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
                'cedula'                => $cedula,
                'password'              => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);
            if ($res->successful()) {
                session()->forget(['recuperar_cedula', 'recuperar_preguntas']);
                return redirect('/login')->with('success', 'Contraseña actualizada correctamente.');
            }
            return back()->with('error', 'Error al actualizar contraseña.')->with('step', 3);
        }
    }

    // ──────────────────────────────────────────────
    // PRIMER LOGIN
    // ──────────────────────────────────────────────

    public function primerLoginForm()
    {
        $res       = $this->api->authGet('/catalogo/preguntas');
        $preguntas = $res->successful() ? ($res->json('data') ?? $res->json()) : [];
        return view('auth.primer-login', compact('preguntas'));
    }

    public function primerLogin(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:6',
            'respuestas'       => 'required|array|size:3',
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
            'preguntas' => collect($request->respuestas)->map(fn($r) => [
                'id'        => $r['id_pregunta'],
                'respuesta' => $r['respuesta'],
            ])->values()->all(),
        ]);

        if (!$res2->successful()) {
            return back()->with('error', 'Contraseña cambiada pero error al guardar preguntas.');
        }

        $user = session('usuario');
        $user['primer_login'] = false;
        session(['usuario' => $user]);

        return $user['rol'] === 'superusuario'
            ? redirect('/admin/usuarios')->with('success', 'Configuración inicial completada.')
            : redirect('/agenda')->with('success', 'Configuración inicial completada.');
    }

    // ──────────────────────────────────────────────
    // PERFIL DE USUARIO
    // ──────────────────────────────────────────────

    public function perfil()
    {
        $usuario = session('usuario');
        $sesionInfo = [
            'login_time' => session('login_time'),
            'login_ip'   => session('login_ip'),
            'login_ua'   => session('login_ua'),
        ];
        $remembered = request()->hasCookie(self::REMEMBER_COOKIE);

        return view('auth.perfil', compact('usuario', 'sesionInfo', 'remembered'));
    }

    // ──────────────────────────────────────────────
    // PANEL DE SESIONES ACTIVAS (solo superusuario)
    // ──────────────────────────────────────────────

    public function sesiones()
    {
        // Llamar al nuevo endpoint para obtener los tokens de sesión activos
        $res = $this->api->authGet('/admin/sesiones');
        $sesionesActivas = $res->successful() ? ($res->json('data') ?? []) : [];

        // Obtener todos los usuarios (opcional, por si queremos listarlos a todos, pero 
        // ahora la vista usará directamente la lista de sesiones)
        $res2 = $this->api->authGet('/admin/usuarios');
        $usuarios = $res2->successful() ? ($res2->json('data') ?? []) : [];

        // La sesión actual
        $sesionActual = [
            'token'      => substr(session('token', ''), 0, 16) . '...',
            'usuario'    => session('usuario'),
            'login_time' => session('login_time'),
            'login_ip'   => session('login_ip'),
            'login_ua'   => session('login_ua'),
        ];

        return view('auth.sesiones', compact('sesionesActivas', 'usuarios', 'sesionActual'));
    }

    // ──────────────────────────────────────────────
    // LOGOUT
    // ──────────────────────────────────────────────

    public function logout(Request $request)
    {
        // Cerrar token en la API
        $this->api->authPost('/auth/logout', []);

        // Destruir sesión
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Eliminar cookie "Recordarme" si existe
        if ($request->hasCookie(self::REMEMBER_COOKIE)) {
            Cookie::queue(Cookie::forget(self::REMEMBER_COOKIE));
        }

        return redirect('/login')->with('success', 'Sesión cerrada correctamente.');
    }
}
