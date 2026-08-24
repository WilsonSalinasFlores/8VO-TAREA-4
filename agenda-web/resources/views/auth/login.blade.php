@extends('layouts.app')

@section('content')
<div class="row justify-content-center min-vh-75 align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px">
                <i class="bi bi-journal-bookmark-fill text-primary fs-2"></i>
            </div>
            <h4 class="fw-bold mb-0">Agenda Virtual</h4>
            <p class="text-muted small">Inicie sesión para continuar</p>
        </div>

        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-4">
                <form action="/login" method="POST" novalidate>
                    @csrf

                    {{-- Cédula --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="cedula">
                            <i class="bi bi-person me-1 text-muted"></i>Cédula
                        </label>
                        <input type="text" id="cedula" name="cedula"
                               class="form-control form-control-lg @error('cedula') is-invalid @enderror"
                               value="{{ $remembered ?? old('cedula') }}"
                               placeholder="Ingrese su cédula"
                               autocomplete="username" autofocus required>
                        @error('cedula')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Contraseña --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password">
                            <i class="bi bi-lock me-1 text-muted"></i>Contraseña
                        </label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                   class="form-control form-control-lg @error('password') is-invalid @enderror"
                                   placeholder="••••••••"
                                   autocomplete="current-password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePwd" tabindex="-1">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Recordarme --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember"
                                   {{ $remembered ? 'checked' : '' }}>
                            <label class="form-check-label small" for="remember">Recordarme</label>
                        </div>
                        <a href="/recuperar" class="text-decoration-none small text-muted">¿Olvidó su contraseña?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </button>

                    <hr class="my-3">
                    <p class="text-center text-muted small mb-0">
                        ¿No tiene cuenta?
                        <a href="/registro" class="fw-semibold text-decoration-none">Regístrese aquí</a>
                    </p>
                </form>
            </div>
        </div>

        @if($remembered)
        <div class="alert alert-info alert-sm mt-3 py-2 px-3 rounded-3 small">
            <i class="bi bi-info-circle me-1"></i>
            Cédula recordada. Marque <strong>Recordarme</strong> para mantenerla.
        </div>
        @endif
    </div>
</div>

<script>
document.getElementById('togglePwd').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        pwd.type = 'password';
        icon.className = 'bi bi-eye';
    }
});
</script>
@endsection
