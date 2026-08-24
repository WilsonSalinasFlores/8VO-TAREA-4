@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">

        <div class="text-center mb-4">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px">
                <i class="bi bi-person-plus-fill text-primary fs-2"></i>
            </div>
            <h4 class="fw-bold mb-0">Crear Cuenta</h4>
            <p class="text-muted small">Complete todos los campos para registrarse</p>
        </div>

        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-4">
                <form action="/registro" method="POST" novalidate>
                    @csrf

                    {{-- Cédula --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="cedula">
                            <i class="bi bi-card-text me-1 text-muted"></i>Cédula <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="cedula" name="cedula"
                               class="form-control @error('cedula') is-invalid @enderror"
                               value="{{ old('cedula') }}"
                               placeholder="10 dígitos" maxlength="10" pattern="\d{10}" required>
                        @error('cedula')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="nombre">
                            <i class="bi bi-person me-1 text-muted"></i>Nombre Completo <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre') }}"
                               placeholder="Su nombre completo" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Correo --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="correo">
                            <i class="bi bi-envelope me-1 text-muted"></i>Correo Electrónico <span class="text-danger">*</span>
                        </label>
                        <input type="email" id="correo" name="correo"
                               class="form-control @error('correo') is-invalid @enderror"
                               value="{{ old('correo') }}"
                               placeholder="correo@ejemplo.com" required>
                        @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Contraseña --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password">
                            <i class="bi bi-lock me-1 text-muted"></i>Contraseña <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Mínimo 8 caracteres (1 mayúscula y 1 número)" minlength="8" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password','eye1')">
                                <i class="bi bi-eye" id="eye1"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirmar Contraseña --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="password_confirmation">
                            <i class="bi bi-lock-fill me-1 text-muted"></i>Confirmar Contraseña <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" placeholder="Repita su contraseña" minlength="8" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password_confirmation','eye2')">
                                <i class="bi bi-eye" id="eye2"></i>
                            </button>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-shield-check me-2 text-primary"></i>Preguntas de Seguridad
                        <small class="text-muted fw-normal">(seleccione 3 distintas)</small>
                    </h6>

                    @for($i = 0; $i < 3; $i++)
                    <div class="mb-3 bg-light rounded-3 p-3">
                        <label class="form-label fw-semibold small">Pregunta {{ $i + 1 }}</label>
                        <select name="respuestas[{{ $i }}][pregunta_id]" class="form-select form-select-sm mb-2" required>
                            <option value="">— Seleccione una pregunta —</option>
                            @foreach($preguntas as $p)
                            <option value="{{ $p['id'] }}">{{ $p['pregunta'] }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="respuestas[{{ $i }}][respuesta]"
                               class="form-control form-control-sm"
                               placeholder="Su respuesta" required autocomplete="off">
                    </div>
                    @endfor

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-semibold mt-2">
                        <i class="bi bi-person-check me-2"></i>Crear Cuenta
                    </button>

                    <p class="text-center text-muted small mt-3 mb-0">
                        ¿Ya tiene cuenta?
                        <a href="/login" class="fw-semibold text-decoration-none">Iniciar sesión</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePwd(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon  = document.getElementById(iconId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endsection
