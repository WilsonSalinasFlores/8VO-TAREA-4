@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Contacto</h4>
                <p class="text-muted small mb-0">Modifique la información y números de teléfono del contacto</p>
            </div>
            <div class="card-body p-4">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="/agenda/{{ $contacto['id'] }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" class="form-control" value="{{ old('nombres', $contacto['nombres']) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos', $contacto['apellidos']) }}" required>
                        </div>

                        {{-- Sección de Teléfonos Estilo Smartphone --}}
                        <div class="col-12 mt-4">
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label fw-bold mb-0 text-primary">
                                        <i class="bi bi-telephone-plus me-1"></i> Teléfonos <span class="text-danger">*</span>
                                    </label>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold" id="btn-add-phone">
                                        <i class="bi bi-plus-circle me-1"></i> Agregar otro número
                                    </button>
                                </div>

                                <div id="telefonos-container">
                                    @php
                                        $telefonosList = old('telefonos', $contacto['telefonos'] ?? []);
                                        if (empty($telefonosList)) {
                                            $telefonosList = [['numero' => $contacto['telefono'] ?? '', 'tipo' => 'Principal']];
                                        }
                                    @endphp
                                    @foreach($telefonosList as $index => $t)
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
                                            <input type="text" name="telefonos[{{ $index }}][numero]" value="{{ $t['numero'] ?? '' }}" class="form-control form-control-sm" placeholder="Ej. 0991234567" required>
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
                            <select name="tipo" class="form-select" required>
                                @foreach(['Personal','Trabajo','Proveedores','Otros'] as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo', $contacto['tipo']) == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-semibold">Dirección <span class="text-danger">*</span></label>
                            <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $contacto['direccion'] ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Empresa <span class="text-muted small">(opcional)</span></label>
                            <input type="text" name="empresa" class="form-control" value="{{ old('empresa', $contacto['empresa'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cargo <span class="text-muted small">(opcional)</span></label>
                            <input type="text" name="cargo" class="form-control" value="{{ old('cargo', $contacto['cargo'] ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Sitio Web <span class="text-muted small">(opcional)</span></label>
                            <input type="url" name="sitio_web" class="form-control" value="{{ old('sitio_web', $contacto['sitio_web'] ?? '') }}" placeholder="https://">
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="/agenda" class="btn btn-secondary rounded-pill">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Actualizar Contacto</button>
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
            if (rows.length === 1) {
                btn.style.display = 'none';
            } else {
                btn.style.display = 'inline-block';
            }
        });
    }

    updateRemoveButtons();
});
</script>
@endsection
