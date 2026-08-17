@extends('layouts.app')

@section('content')
<h2 class="mb-4"><i class="bi bi-people-fill me-2"></i>Gestión de Usuarios</h2>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $u)
                    <tr>
                        <td>{{ $u['cedula'] }}</td>
                        <td>{{ $u['nombre'] }}</td>
                        <td>{{ $u['correo'] }}</td>
                        <td><span class="badge bg-secondary">{{ $u['rol'] }}</span></td>
                        <td>
                            @if($u['estado'] === 'ACTIVO')
                                <span class="badge bg-success">ACTIVO</span>
                            @elseif($u['estado'] === 'INACTIVO')
                                <span class="badge bg-danger">INACTIVO</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ $u['estado'] }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#agenda-{{ $u['id'] }}" onclick="cargarAgenda({{ $u['id'] }})">
                                <i class="bi bi-eye"></i> Ver Agenda
                            </button>
                            @if($u['id'] !== session('usuario')['id'])
                            <form action="/admin/usuarios/{{ $u['id'] }}/estado" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-repeat"></i> Cambiar Estado
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    <tr class="p-0 border-0">
                        <td colspan="6" class="p-0 border-0">
                            <div class="collapse" id="agenda-{{ $u['id'] }}">
                                <div class="p-3 bg-light border-bottom" id="agenda-content-{{ $u['id'] }}">
                                    <div class="text-center text-muted"><div class="spinner-border spinner-border-sm"></div> Cargando agenda...</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function cargarAgenda(id) {
    const container = document.getElementById('agenda-content-' + id);
    if(container.dataset.loaded) return;
    
    fetch('/admin/usuarios/' + id + '/agenda')
        .then(res => res.json())
        .then(data => {
            container.dataset.loaded = 'true';
            if(!data || data.length === 0) {
                container.innerHTML = '<p class="text-muted mb-0">No hay contactos en la agenda.</p>';
                return;
            }
            
            let html = '<div class="row row-cols-1 row-cols-md-3 g-3">';
            data.forEach(c => {
                let badge = c.estado === 'ACTIVO' ? '<span class="badge bg-success">ACTIVO</span>' : '<span class="badge bg-danger">ELIMINADO</span>';
                html += `
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="fw-bold mb-1">${c.nombres} ${c.apellidos} ${badge}</h6>
                            <p class="mb-1 small"><i class="bi bi-telephone-fill text-muted me-1"></i>${c.telefono}</p>
                            <p class="mb-0 small"><span class="badge bg-secondary">${c.tipo}</span></p>
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        })
        .catch(() => {
            container.innerHTML = '<p class="text-danger mb-0">Error al cargar la agenda.</p>';
        });
}
</script>
@endsection
