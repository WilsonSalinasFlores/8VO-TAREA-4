@extends('layouts.app')

@section('content')
<h2 class="mb-4"><i class="bi bi-journal-check me-2"></i>Bitácora de Eventos</h2>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Tabla Afectada</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bitacora as $b)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($b['fecha_hora'])->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $b['usuario']['nombre'] ?? 'Sistema' }} <br><small class="text-muted">{{ $b['usuario']['cedula'] ?? '' }}</small></td>
                        <td>
                            @if($b['accion'] === 'INSERT')
                                <span class="badge bg-success">INSERT</span>
                            @elseif($b['accion'] === 'UPDATE')
                                <span class="badge bg-warning text-dark">UPDATE</span>
                            @elseif($b['accion'] === 'DELETE')
                                <span class="badge bg-danger">DELETE</span>
                            @else
                                <span class="badge bg-secondary">{{ $b['accion'] }}</span>
                            @endif
                        </td>
                        <td>{{ $b['tabla_afectada'] }}</td>
                        <td>{{ $b['ip_origen'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No hay registros en la bitácora.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
