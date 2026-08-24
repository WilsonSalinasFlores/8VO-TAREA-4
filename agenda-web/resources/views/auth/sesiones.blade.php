@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2 text-info"></i>Panel de Sesiones</h4>
        <p class="text-muted small mb-0">Gestión de sesiones y usuarios del sistema</p>
    </div>
    <a href="/admin/usuarios" class="btn btn-outline-secondary btn-sm rounded-pill">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

{{-- ── Sesión actual ── --}}
<div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-success border-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-1">
        <h6 class="fw-semibold text-success mb-0">
            <i class="bi bi-check-circle-fill me-2"></i>Su sesión activa
        </h6>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                    <i class="bi bi-person-check text-success fs-4"></i>
                </div>
            </div>
            <div class="col">
                <p class="fw-bold mb-0">
                    {{ $sesionActual['usuario']['nombre'] ?? '—' }}
                    <span class="badge bg-danger ms-1">{{ $sesionActual['usuario']['rol'] ?? '' }}</span>
                </p>
                <p class="text-muted small mb-0">Cédula: {{ $sesionActual['usuario']['cedula'] ?? '—' }}</p>
            </div>
            <div class="col-12 col-md-auto">
                <table class="table table-sm table-borderless mb-0 small">
                    <tr>
                        <td class="text-muted pe-3">Inicio:</td>
                        <td class="fw-semibold">
                            @if($sesionActual['login_time'])
                                {{ \Carbon\Carbon::parse($sesionActual['login_time'])->format('d/m/Y H:i:s') }}
                                <span class="text-muted fw-normal">({{ \Carbon\Carbon::parse($sesionActual['login_time'])->diffForHumans() }})</span>
                            @else —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted pe-3">IP:</td>
                        <td class="fw-semibold">{{ $sesionActual['login_ip'] ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted pe-3">Token:</td>
                        <td><code class="small">{{ $sesionActual['token'] }}</code></td>
                    </tr>
                </table>
            </div>
            <div class="col-12 col-md-auto text-md-end">
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill"
                            onclick="return confirm('¿Cerrar su sesión actual?')">
                        <i class="bi bi-box-arrow-right me-1"></i>Cerrar mi sesión
                    </button>
                </form>
            </div>
        </div>
        @if($sesionActual['login_ua'])
        <div class="mt-2">
            <small class="text-muted"><i class="bi bi-display me-1"></i>{{ $sesionActual['login_ua'] }}</small>
        </div>
        @endif
    </div>
</div>

{{-- ── Lista de Sesiones Activas ── --}}
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-1">
        <h6 class="fw-semibold mb-0">
            <i class="bi bi-activity me-2"></i>Tokens de Sesión Activos
            <span class="badge bg-secondary ms-1">{{ count($sesionesActivas) }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Usuario</th>
                        <th>Rol</th>
                        <th>Creado</th>
                        <th>Último uso</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesionesActivas as $sesion)
                    @php
                        $u = $sesion['tokenable'];
                        $esSesionActual = ($u['id'] ?? null) === ($sesionActual['usuario']['id'] ?? -1);
                    @endphp
                    <tr class="{{ $esSesionActual ? 'table-success' : '' }}">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px">
                                    <i class="bi bi-person text-secondary"></i>
                                </div>
                                <div>
                                    <p class="fw-semibold mb-0 small">{{ $u['nombre'] ?? '—' }}</p>
                                    <p class="text-muted mb-0" style="font-size:.75rem">{{ $u['cedula'] ?? '—' }} | Token ID: {{ $sesion['id'] }}</p>
                                </div>
                                @if($esSesionActual)
                                    <span class="badge bg-success ms-1">Usted</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ ($u['rol'] ?? '') === 'superusuario' ? 'bg-danger' : 'bg-primary' }} rounded-pill">
                                {{ $u['rol'] ?? '—' }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            {{ \Carbon\Carbon::parse($sesion['created_at'])->format('d/m/y H:i') }}
                        </td>
                        <td class="small">
                            @if($sesion['last_used_at'])
                                {{ \Carbon\Carbon::parse($sesion['last_used_at'])->diffForHumans() }}
                            @else
                                <span class="text-muted">Nunca</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($esSesionActual)
                                <i class="bi bi-circle-fill text-success" title="Sesión activa"></i>
                                <small class="text-success ms-1">En línea</small>
                            @else
                                <i class="bi bi-circle-fill text-info" title="Sesión abierta"></i>
                                <small class="text-info ms-1">Activo</small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-slash-circle fs-3 d-block mb-2"></i>No hay sesiones activas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Lista de usuarios del sistema ── --}}
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-1">
        <h6 class="fw-semibold mb-0">
            <i class="bi bi-people me-2"></i>Todos los usuarios registrados
            <span class="badge bg-secondary ms-1">{{ count($usuarios) }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Usuario</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $u)
                    <tr>
                        <td class="ps-4">
                            <p class="fw-semibold mb-0 small">{{ $u['nombre'] ?? '—' }}</p>
                            <p class="text-muted mb-0" style="font-size:.75rem">{{ $u['cedula'] ?? '—' }}</p>
                        </td>
                        <td class="small">{{ $u['correo'] ?? '—' }}</td>
                        <td>
                            <span class="badge {{ ($u['rol'] ?? '') === 'superusuario' ? 'bg-danger' : 'bg-primary' }} rounded-pill">
                                {{ $u['rol'] ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @if($u['is_active'] ?? true)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 text-muted small px-4 py-3">
        <i class="bi bi-info-circle me-1"></i>
        Sanctum gestiona los tokens en la API de forma independiente.
    </div>
</div>
@endsection
