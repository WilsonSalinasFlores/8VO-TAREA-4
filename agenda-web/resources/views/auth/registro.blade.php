@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h3 class="text-center mb-4"><i class="bi bi-person-plus-fill text-primary me-2"></i>Crear Cuenta</h3>
                <form action="/registro" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Cédula (10 dígitos)</label>
                        <input type="text" name="cedula" class="form-control" required pattern="\d{10}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    
                    <hr class="my-4">
                    <h5 class="mb-3">Preguntas de Seguridad</h5>
                    
                    @for($i = 0; $i < 3; $i++)
                    <div class="mb-3 border p-3 rounded">
                        <label class="form-label">Pregunta {{ $i + 1 }}</label>
                        <select name="respuestas[{{ $i }}][pregunta_id]" class="form-select mb-2" required>
                            <option value="">Seleccione una pregunta...</option>
                            @foreach($preguntas as $p)
                            <option value="{{ $p['id'] }}">{{ $p['pregunta'] }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="respuestas[{{ $i }}][respuesta]" class="form-control" placeholder="Su respuesta" required>
                    </div>
                    @endfor

                    <button type="submit" class="btn btn-primary w-100 rounded-pill mb-3">Registrarse</button>
                    <div class="text-center">
                        <a href="/login" class="text-decoration-none">Ya tengo cuenta</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
