@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-person-plus-fill text-primary me-2"></i>Nuevo Contacto</h4>
                <p class="text-muted small mb-0">Agregue la información y números de teléfono del contacto</p>
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
                            <input type="text" name="nombres" value="{{ old('nombres') }}" class="form-control @error('nombres') is-invalid @enderror" placeholder="Ej. Juan" autofocus required>
                            @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" value="{{ old('apellidos') }}" class="form-control @error('apellidos') is-invalid @enderror" placeholder="Ej. Pérez" required>
                            @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Sección de Teléfonos Estilo Smartphone --}}
                        <div class="col-12 mt-4">
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <label class="form-label fw-bold mb-0 text-primary">
                                            <i class="bi bi-telephone-plus me-1"></i> Teléfonos del Contacto <span class="text-danger">*</span>
                                        </label>
                                        <small class="text-muted d-block" style="font-size: 0.78rem;">Especifique el tipo (Celular, Trabajo, Casa, etc.)</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold" id="btn-add-phone">
                                        <i class="bi bi-plus-circle me-1"></i> Agregar otro número
                                    </button>
                                </div>

                                <div id="telefonos-container">
                                    @php
                                        $oldTelefonos = old('telefonos', [['numero' => '', 'tipo' => 'Celular']]);
                                    @endphp
                                    @foreach($oldTelefonos as $index => $t)
                                    <div class="row g-2 mb-2 align-items-center telefono-row">
                                        <div class="col-md-4">
                                            <select name="telefonos[{{ $index }}][tipo]" class="form-select form-select-sm" required>
                                                <option value="Celular" {{ ($t['tipo'] ?? '') == 'Celular' ? 'selected' : '' }}>📱 Celular</option>
                                                <option value="Trabajo" {{ ($t['tipo'] ?? '') == 'Trabajo' ? 'selected' : '' }}>🏢 Trabajo</option>
                                                <option value="Casa" {{ ($t['tipo'] ?? '') == 'Casa' ? 'selected' : '' }}>🏠 Casa</option>
                                                <option value="Principal" {{ ($t['tipo'] ?? '') == 'Principal' ? 'selected' : '' }}>⭐ Principal</option>
                                                <option value="Otros" {{ ($t['tipo'] ?? '') == 'Otros' ? 'selected' : '' }}>📞 Otro</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <input type="text" name="telefonos[{{ $index }}][numero]" value="{{ $t['numero'] ?? '' }}" class="form-control form-control-sm @error('telefonos.'.$index.'.numero') is-invalid @enderror" placeholder="Ej. 0991234567" required>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-phone" title="Eliminar número">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-semibold">Tipo de Contacto <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach(['Personal','Trabajo','Proveedores','Otros'] as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </select>
                            @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-semibold">Dirección <span class="text-danger">*</span></label>
                            <input type="text" name="direccion" value="{{ old('direccion') }}" class="form-control @error('direccion') is-invalid @enderror" placeholder="Ej. Av. Amazonas N24-12" required>
                            @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Empresa <span class="text-muted small">(opcional)</span></label>
                            <input type="text" name="empresa" value="{{ old('empresa') }}" class="form-control" placeholder="Ej. Corporación XYZ">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cargo <span class="text-muted small">(opcional)</span></label>
                            <input type="text" name="cargo" value="{{ old('cargo') }}" class="form-control" placeholder="Ej. Gerente de Ventas">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Sitio Web <span class="text-muted small">(opcional)</span></label>
                            <input type="url" name="sitio_web" value="{{ old('sitio_web') }}" class="form-control @error('sitio_web') is-invalid @enderror" placeholder="https://ejemplo.com">
                            @error('sitio_web')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="/agenda" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold"><i class="bi bi-save me-1"></i>Guardar Contacto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let phoneIndex = document.querySelectorAll('.telefono-row').length;
    const container = document.getElementById('telefonos-container');

    document.getElementById('btn-add-phone').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 align-items-center telefono-row';
        row.innerHTML = `
            <div class="col-md-4">
                <select name="telefonos[${phoneIndex}][tipo]" class="form-select form-select-sm" required>
                    <option value="Celular" selected>📱 Celular</option>
                    <option value="Trabajo">🏢 Trabajo</option>
                    <option value="Casa">🏠 Casa</option>
                    <option value="Principal">⭐ Principal</option>
                    <option value="Otros">📞 Otro</option>
                </select>
            </div>
            <div class="col">
                <input type="text" name="telefonos[${phoneIndex}][numero]" class="form-control form-control-sm" placeholder="Ej. 0991234567" required>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-phone" title="Eliminar número">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        phoneIndex++;
        updateRemoveButtons();
        row.querySelector('input').focus();
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-phone')) {
            const rows = container.querySelectorAll('.telefono-row');
            if (rows.length > 1) {
                e.target.closest('.telefono-row').remove();
            }
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.telefono-row');
        rows.forEach(r => {
            const btn = r.querySelector('.btn-remove-phone');
            btn.style.display = rows.length === 1 ? 'none' : 'inline-block';
        });
    }

    updateRemoveButtons();
});
</script>
@endsection
