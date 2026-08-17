@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h3 class="mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Contacto</h3>
            </div>
            <div class="card-body p-4">
                <form action="/agenda/{{ $contacto['id'] }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombres</label>
                            <input type="text" name="nombres" class="form-control" value="{{ $contacto['nombres'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" value="{{ $contacto['apellidos'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ $contacto['telefono'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico (Opcional)</label>
                            <input type="email" name="correo" class="form-control" value="{{ $contacto['correo'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Contacto</label>
                            <select name="tipo" class="form-select" required>
                                <option value="Personal" {{ $contacto['tipo'] == 'Personal' ? 'selected' : '' }}>Personal</option>
                                <option value="Trabajo" {{ $contacto['tipo'] == 'Trabajo' ? 'selected' : '' }}>Trabajo</option>
                                <option value="Proveedores" {{ $contacto['tipo'] == 'Proveedores' ? 'selected' : '' }}>Proveedores</option>
                                <option value="Otros" {{ $contacto['tipo'] == 'Otros' ? 'selected' : '' }}>Otros</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="/agenda" class="btn btn-secondary rounded-pill">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill">Actualizar Contacto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
