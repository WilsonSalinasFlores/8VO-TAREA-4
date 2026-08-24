@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">

        {{-- Encabezado --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px">
                <i class="bi bi-person-circle text-primary fs-3"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Mi Perfil</h4>
                <p class="mb-0 text-muted small">Información de su cuenta y sesión actual</p>
            </div>
        </div>

        {{-- Card: Datos del usuario --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-semibold text-uppercase text-muted small mb-0">
                    <i class="bi bi-person-badge me-2"></i>Datos de la cuenta
                </h6>
            </div>
            <div class="card-body px-4 py-3">
                <div class="row g-3">
                    <div class="col-6">
                        <p class="text-muted small mb-1">Nombre</p>
                        <p class="fw-semibold mb-0">{{ $usuario['nombre'] ?? '—' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small mb-1">Cédula</p>
                        <p class="fw-semibold mb-0">{{ $usuario['cedula'] ?? '—' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small mb-1">Correo</p>
                        <p class="fw-semibold mb-0">{{ $usuario['correo'] ?? '—' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small mb-1">Rol</p>
                        <span class="badge {{ $usuario['rol'] === 'superusuario' ? 'bg-danger' : 'bg-primary' }} rounded-pill">
                            <i class="bi bi-{{ $usuario['rol'] === 'superusuario' ? 'shield-lock' : 'person' }} me-1"></i>
                            {{ ucfirst($usuario['rol']) }}
                        </span>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small mb-1">Estado de cuenta</p>
                        @if($usuario['is_active'] ?? true)
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-danger">Inactiva</span>
                        @endif
                    </div>
                    <div class="col-6">
                        <p class="text-muted small mb-1">Recordarme activo</p>
                        @if($remembered)
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-cookie me-1"></i>Sí (30 días)
                            </span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Información de sesión --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-semibold text-uppercase text-muted small mb-0">
                    <i class="bi bi-clock-history me-2"></i>Sesión actual
                </h6>
            </div>
            <div class="card-body px-4 py-3">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <p class="text-muted small mb-1">Inicio de sesión</p>
                        <p class="fw-semibold mb-0">
                            @if($sesionInfo['login_time'])
                                {{ \Carbon\Carbon::parse($sesionInfo['login_time'])->format('d/m/Y H:i:s') }}
                                <br><small class="text-muted fw-normal">
                                    ({{ \Carbon\Carbon::parse($sesionInfo['login_time'])->diffForHumans() }})
                                </small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-12 col-md-6">
                        <p class="text-muted small mb-1">IP de acceso</p>
                        <p class="fw-semibold mb-0">
                            <i class="bi bi-geo-alt text-muted me-1"></i>
                            {{ $sesionInfo['login_ip'] ?? '—' }}
                        </p>
                    </div>
                    <div class="col-12">
                        <p class="text-muted small mb-1">Navegador / Dispositivo</p>
                        <p class="fw-semibold mb-0 small text-truncate" title="{{ $sesionInfo['login_ua'] ?? '' }}">
                            <i class="bi bi-display text-muted me-1"></i>
                            {{ Str::limit($sesionInfo['login_ua'] ?? '—', 80) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ session('rol') === 'superusuario' ? '/admin/usuarios' : '/agenda' }}"
               class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>

            @if(session('rol') === 'superusuario')
            <a href="/admin/sesiones" class="btn btn-outline-info rounded-pill">
                <i class="bi bi-people me-1"></i>Ver sesiones activas
            </a>
            @endif

            <form action="/logout" method="POST" class="ms-auto">
                @csrf
                <button type="submit" class="btn btn-danger rounded-pill"
                        onclick="return confirm('¿Está seguro que desea cerrar sesión?')">
                    <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
