@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h3 class="mb-0"><i class="bi bi-person-plus text-primary me-2"></i>Nuevo Contacto</h3>
            </div>
            <div class="card-body p-4">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form action="/agenda" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" value="{{ old('nombres') }}" class="form-control @error('nombres') is-invalid @enderror" required>
                            @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" value="{{ old('apellidos') }}" class="form-control @error('apellidos') is-invalid @enderror" required>
                            @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control @error('telefono') is-invalid @enderror" placeholder="7 a 15 dígitos" required>
                            @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Contacto <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach(['Personal','Trabajo','Proveedores','Otros'] as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </select>
                            @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dirección <span class="text-danger">*</span></label>
                            <input type="text" name="direccion" value="{{ old('direccion') }}" class="form-control @error('direccion') is-invalid @enderror" required>
                            @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Empresa <span class="text-muted small">(opcional)</span></label>
                            <input type="text" name="empresa" value="{{ old('empresa') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cargo <span class="text-muted small">(opcional)</span></label>
                            <input type="text" name="cargo" value="{{ old('cargo') }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Sitio Web <span class="text-muted small">(opcional)</span></label>
                            <input type="url" name="sitio_web" value="{{ old('sitio_web') }}" class="form-control @error('sitio_web') is-invalid @enderror" placeholder="https://">
                            @error('sitio_web')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="/agenda" class="btn btn-secondary rounded-pill">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill"><i class="bi bi-save me-1"></i>Guardar Contacto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
