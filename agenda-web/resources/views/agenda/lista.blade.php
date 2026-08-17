@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-journal-text me-2"></i>Mis Contactos</h2>
    <a href="/agenda/nuevo" class="btn btn-primary rounded-pill"><i class="bi bi-plus-lg me-1"></i>Nuevo Contacto</a>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <form action="/agenda" method="GET" class="d-flex">
            <input type="search" name="q" class="form-control me-2" placeholder="Buscar por nombre, apellido o teléfono..." value="{{ $q ?? '' }}">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
            @if($q)
            <a href="/agenda" class="btn btn-outline-secondary ms-2"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    @forelse($contactos as $c)
    <div class="col">
        <div class="card h-100 shadow-sm border-0 rounded-4 {{ 
            $c['tipo'] === 'Trabajo' ? 'border-success' : 
            ($c['tipo'] === 'Personal' ? 'border-info' : 
            ($c['tipo'] === 'Proveedores' ? 'border-warning' : 'border-secondary')) 
        }} border-top border-5">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title fw-bold mb-0">{{ $c['nombres'] }} {{ $c['apellidos'] }}</h5>
                    <span class="badge {{ 
                        $c['tipo'] === 'Trabajo' ? 'bg-success' : 
                        ($c['tipo'] === 'Personal' ? 'bg-info' : 
                        ($c['tipo'] === 'Proveedores' ? 'bg-warning' : 'bg-secondary')) 
                    }}">{{ $c['tipo'] }}</span>
                </div>
                <p class="card-text mb-1"><i class="bi bi-telephone-fill text-muted me-2"></i>{{ $c['telefono'] }}</p>
                @if(!empty($c['correo']))
                <p class="card-text"><i class="bi bi-envelope-fill text-muted me-2"></i>{{ $c['correo'] }}</p>
                @endif
            </div>
            <div class="card-footer bg-transparent border-0 pt-0 d-flex justify-content-end gap-2">
                <a href="/agenda/{{ $c['id'] }}/editar" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form action="/agenda/{{ $c['id'] }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar contacto?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-light text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
            <p class="mb-0 text-muted">No se encontraron contactos.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
