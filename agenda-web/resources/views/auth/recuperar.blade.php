@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h3 class="text-center mb-4"><i class="bi bi-key-fill text-primary me-2"></i>Recuperar Contraseña</h3>
                
                @if(session('step', 1) == 1)
                <form action="/recuperar" method="POST">
                    @csrf
                    <input type="hidden" name="step" value="1">
                    <p class="text-muted">Ingrese su cédula para buscar sus preguntas de seguridad.</p>
                    <div class="mb-4">
                        <label class="form-label">Cédula</label>
                        <input type="text" name="cedula" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill mb-3">Siguiente</button>
                    <div class="text-center">
                        <a href="/login" class="text-decoration-none">Volver al login</a>
                    </div>
                </form>
                @endif

                @if(session('step') == 2)
                <form action="/recuperar" method="POST">
                    @csrf
                    <input type="hidden" name="step" value="2">
                    <p class="text-muted">Responda a sus preguntas de seguridad.</p>
                    
                    @foreach(session('recuperar_preguntas') as $i => $p)
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $p['pregunta'] }}</label>
                        <input type="hidden" name="respuestas[{{ $i }}][pregunta_id]" value="{{ $p['id'] }}">
                        <input type="text" name="respuestas[{{ $i }}][respuesta]" class="form-control" required>
                    </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Verificar Respuestas</button>
                </form>
                @endif

                @if(session('step') == 3)
                <form action="/recuperar" method="POST">
                    @csrf
                    <input type="hidden" name="step" value="3">
                    <p class="text-muted">Ingrese su nueva contraseña.</p>
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Guardar Contraseña</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
