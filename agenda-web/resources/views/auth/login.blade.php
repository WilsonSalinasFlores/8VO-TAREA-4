@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h3 class="text-center mb-4"><i class="bi bi-box-arrow-in-right text-primary me-2"></i>Iniciar Sesión</h3>
                <form action="/login" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Cédula</label>
                        <input type="text" name="cedula" class="form-control" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill mb-3">Entrar</button>
                    <div class="d-flex justify-content-between">
                        <a href="/registro" class="text-decoration-none">Crear cuenta</a>
                        <a href="/recuperar" class="text-decoration-none">¿Olvidó su contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
