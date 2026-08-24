@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h2 class="mb-0"><i class="bi bi-journal-check me-2"></i>Bitácora de Eventos</h2>
    <span class="text-muted small">
        Total: <strong>{{ $paginacion['total'] }}</strong> registros
    </span>
</div>

{{-- ── FILTROS ── --}}
<form method="GET" action="{{ url('/admin/bitacora') }}" class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">

            {{-- Filtro por usuario --}}
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small mb-1">
                    <i class="bi bi-person me-1"></i>Usuario (nombre o cédula)
                </label>
                <input type="text" name="usuario" class="form-control form-control-sm"
                       placeholder="Buscar usuario..." value="{{ $filtros['usuario'] ?? '' }}">
            </div>

            {{-- Filtro por acción --}}
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small mb-1">
                    <i class="bi bi-funnel me-1"></i>Acción
                </label>
                <select name="accion" class="form-select form-select-sm">
                    <option value="">— Todas las acciones —</option>
                    @foreach($acciones as $a)
                        <option value="{{ $a }}" {{ ($filtros['accion'] ?? '') === $a ? 'selected' : '' }}>
                            {{ $a }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Registros por página --}}
            <div class="col-12 col-md-2">
                <label class="form-label fw-semibold small mb-1">
                    <i class="bi bi-list-ol me-1"></i>Mostrar
                </label>
                <select name="per_page" class="form-select form-select-sm">
                    @foreach([10, 20, 50, 100] as $n)
                        <option value="{{ $n }}" {{ (int)$perPage === $n ? 'selected' : '' }}>
                            {{ $n }} por página
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Botones --}}
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <a href="{{ url('/admin/bitacora') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

        </div>
    </div>
</form>

{{-- ── TABLA ── --}}
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width:140px">Fecha y Hora</th>
                        <th>Administrador / Usuario</th>
                        <th>Usuario Afectado</th>
                        <th style="width:160px">Acción</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bitacora as $b)
                    <tr>
                        {{-- Fecha --}}
                        <td class="ps-3 text-nowrap small text-muted">
                            @if($b['fecha_hora'])
                                {{ \Carbon\Carbon::parse($b['fecha_hora'])->format('d/m/Y') }}<br>
                                <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($b['fecha_hora'])->format('H:i:s') }}</span>
                            @else
                                —
                            @endif
                        </td>

                        {{-- Admin/superusuario --}}
                        <td>
                            @php $admin = $b['usuario'] ?? $b['superusuario'] ?? null; @endphp
                            @if(is_array($admin))
                                <span class="fw-semibold">{{ $admin['nombre'] ?? '—' }}</span>
                                <br><small class="text-muted">{{ $admin['cedula'] ?? '' }}</small>
                            @else
                                <span class="text-muted">Sistema</span>
                            @endif
                        </td>

                        {{-- Target usuario --}}
                        <td>
                            @php $target = $b['target_usuario'] ?? null; @endphp
                            @if(is_array($target) && !empty($target['nombre']))
                                <span class="fw-semibold">{{ $target['nombre'] }}</span>
                                <br><small class="text-muted">{{ $target['cedula'] ?? '' }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Acción con badge de color --}}
                        <td>
                            @php $accion = strtoupper($b['accion'] ?? ''); @endphp
                            @if(str_contains($accion, 'CREAR') || str_contains($accion, 'REGISTRO') || str_contains($accion, 'INSERT'))
                                <span class="badge bg-success">{{ $b['accion'] }}</span>
                            @elseif(str_contains($accion, 'EDITAR') || str_contains($accion, 'UPDATE') || str_contains($accion, 'ESTADO'))
                                <span class="badge bg-warning text-dark">{{ $b['accion'] }}</span>
                            @elseif(str_contains($accion, 'ELIMINAR') || str_contains($accion, 'DELETE'))
                                <span class="badge bg-danger">{{ $b['accion'] }}</span>
                            @elseif(str_contains($accion, 'LOGIN'))
                                <span class="badge bg-info text-dark">{{ $b['accion'] }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $b['accion'] }}</span>
                            @endif
                        </td>

                        {{-- Descripción --}}
                        <td class="small">{{ $b['descripcion'] ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                            No hay registros en la bitácora con los filtros aplicados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── PAGINACIÓN ── --}}
    @if($paginacion['last_page'] > 1)
    <div class="card-footer bg-white border-top-0 d-flex align-items-center justify-content-between py-3 px-4">
        {{-- Info --}}
        <small class="text-muted">
            Mostrando <strong>{{ $paginacion['from'] }}</strong>–<strong>{{ $paginacion['to'] }}</strong>
            de <strong>{{ $paginacion['total'] }}</strong> registros
        </small>

        {{-- Controles --}}
        <nav>
            <ul class="pagination pagination-sm mb-0">
                {{-- Anterior --}}
                <li class="page-item {{ $paginacion['current_page'] <= 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $paginacion['current_page'] - 1]) }}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                {{-- Páginas --}}
                @php
                    $curr  = $paginacion['current_page'];
                    $last  = $paginacion['last_page'];
                    $start = max(1, $curr - 2);
                    $end   = min($last, $curr + 2);
                @endphp

                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">1</a>
                    </li>
                    @if($start > 2)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                @endif

                @for($p = $start; $p <= $end; $p++)
                    <li class="page-item {{ $p === $curr ? 'active' : '' }}">
                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $p]) }}">{{ $p }}</a>
                    </li>
                @endfor

                @if($end < $last)
                    @if($end < $last - 1)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                    <li class="page-item">
                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $last]) }}">{{ $last }}</a>
                    </li>
                @endif

                {{-- Siguiente --}}
                <li class="page-item {{ $paginacion['current_page'] >= $last ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $paginacion['current_page'] + 1]) }}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    @else
    <div class="card-footer bg-white border-top-0 py-2 px-4">
        <small class="text-muted">
            Mostrando <strong>{{ $paginacion['from'] }}</strong>–<strong>{{ $paginacion['to'] }}</strong>
            de <strong>{{ $paginacion['total'] }}</strong> registros
        </small>
    </div>
    @endif
</div>
@endsection
