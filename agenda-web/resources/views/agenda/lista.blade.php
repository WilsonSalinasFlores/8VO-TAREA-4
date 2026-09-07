@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-0 text-dark">
            <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>Mis Contactos
        </h3>
        <p class="text-muted small mb-0">
            Mostrando <span class="badge bg-primary rounded-pill">{{ count($contactos) }}</span> contactos en su agenda
        </p>
    </div>
    <a href="/agenda/nuevo" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Contacto
    </a>
</div>

{{-- Barra de Búsqueda --}}
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-2 p-md-3">
        <form action="/agenda" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0 ps-0" placeholder="Buscar por nombre, apellido, teléfono, empresa..." value="{{ $q ?? '' }}">
            </div>
            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">Buscar</button>
            @if($q)
            <a href="/agenda" class="btn btn-outline-secondary rounded-3" title="Restablecer búsqueda"><i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar</a>
            @endif
        </form>
    </div>
</div>

{{-- Lista de Tarjetas de Contactos --}}
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    @forelse($contactos as $c)
    <div class="col">
        <div class="card h-100 shadow-sm border-0 rounded-4 contact-card {{ 
            $c['tipo'] === 'Trabajo' ? 'border-success' : 
            ($c['tipo'] === 'Personal' ? 'border-info' : 
            ($c['tipo'] === 'Proveedores' ? 'border-warning' : 'border-secondary')) 
        }} border-top border-4">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="card-title fw-bold mb-1 text-dark">{{ $c['nombres'] }} {{ $c['apellidos'] }}</h5>
                        @if(!empty($c['empresa']) || !empty($c['cargo']))
                        <p class="text-muted small mb-0">
                            <i class="bi bi-building me-1 text-secondary"></i>
                            {{ $c['cargo'] ?? '' }}{{ !empty($c['cargo']) && !empty($c['empresa']) ? ' en ' : '' }}<strong>{{ $c['empresa'] ?? '' }}</strong>
                        </p>
                        @endif
                    </div>
                    <span class="badge {{ 
                        $c['tipo'] === 'Trabajo' ? 'bg-success' : 
                        ($c['tipo'] === 'Personal' ? 'bg-info text-dark' : 
                        ($c['tipo'] === 'Proveedores' ? 'bg-warning text-dark' : 'bg-secondary')) 
                    }} rounded-pill px-3 py-1">{{ $c['tipo'] }}</span>
                </div>

                {{-- Lista de Teléfonos Estilo Smartphone --}}
                <div class="my-3 p-2 bg-light rounded-3 border flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            <i class="bi bi-telephone-fill me-1 text-primary"></i>Teléfonos
                        </small>
                    </div>

                    @php
                        $phoneList = $c['telefonos'] ?? [];
                        if (empty($phoneList) && !empty($c['telefono'])) {
                            $phoneList = [['numero' => $c['telefono'], 'tipo' => 'Principal']];
                        }
                    @endphp

                    @forelse($phoneList as $phone)
                    @php
                        $cleanNum = preg_replace('/[^0-9+]/', '', $phone['numero']);
                    @endphp
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle last-border-none">
                        <div class="d-flex align-items-center gap-1">
                            <span class="small me-1">
                                @switch($phone['tipo'] ?? '')
                                    @case('Celular') 📱 @break
                                    @case('Trabajo') 🏢 @break
                                    @case('Casa') 🏠 @break
                                    @case('Principal') ⭐ @break
                                    @default 📞
                                @endswitch
                            </span>
                            <a href="tel:{{ $cleanNum }}" class="fw-semibold text-dark text-decoration-none font-monospace small" title="Llamar">
                                {{ $phone['numero'] }}
                            </a>
                        </div>
                        
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-white text-secondary border small me-1" style="font-size: 0.68rem;">
                                {{ $phone['tipo'] ?? 'Teléfono' }}
                            </span>
                            <a href="https://wa.me/{{ $cleanNum }}" target="_blank" class="btn btn-sm btn-light p-0 px-1 text-success phone-action-btn" title="Enviar WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <button type="button" onclick="copyToClipboard('{{ $phone['numero'] }}')" class="btn btn-sm btn-light p-0 px-1 text-muted phone-action-btn" title="Copiar número">
                                <i class="bi bi-copy"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <span class="text-muted small">Sin teléfonos registrados</span>
                    @endforelse
                </div>

                @if(!empty($c['direccion']))
                <p class="card-text small text-muted mb-1"><i class="bi bi-geo-alt me-2 text-danger"></i>{{ $c['direccion'] }}</p>
                @endif
                @if(!empty($c['sitio_web']))
                <p class="card-text small mb-0"><i class="bi bi-globe me-2 text-primary"></i><a href="{{ $c['sitio_web'] }}" target="_blank" class="text-decoration-none text-truncate d-inline-block style='max-width: 200px'">{{ $c['sitio_web'] }}</a></p>
                @endif
            </div>

            <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4 d-flex justify-content-end gap-2">
                <a href="/agenda/{{ $c['id'] }}/editar" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
                <form action="/agenda/{{ $c['id'] }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar a {{ $c['nombres'] }} de su agenda?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                        <i class="bi bi-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-white text-center py-5 rounded-4 shadow-sm border">
            <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                <i class="bi bi-journal-x fs-1 text-muted"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">No se encontraron contactos</h5>
            <p class="text-muted small mb-3">
                @if($q)
                    No hay resultados para "{{ $q }}". Intente con otro término de búsqueda.
                @else
                    Aún no tiene contactos agregados a su agenda.
                @endif
            </p>
            @if($q)
                <a href="/agenda" class="btn btn-outline-secondary rounded-pill px-4">Ver todos los contactos</a>
            @else
                <a href="/agenda/nuevo" class="btn btn-primary rounded-pill px-4"><i class="bi bi-plus-lg me-1"></i>Crear mi primer contacto</a>
            @endif
        </div>
    </div>
    @endforelse
</div>
@endsection
