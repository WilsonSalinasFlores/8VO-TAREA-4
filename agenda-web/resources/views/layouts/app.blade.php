<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Virtual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { 
            background-color: #f4f6f8; 
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .contact-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .contact-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
        }
        .phone-action-btn {
            opacity: 0.7;
            transition: opacity 0.15s ease;
            text-decoration: none;
        }
        .phone-action-btn:hover {
            opacity: 1;
        }
        .toast-copy {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1080;
            display: none;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ session('rol') === 'superusuario' ? '/admin/usuarios' : '/agenda' }}">
                <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>Agenda Virtual
            </a>

            @if(session('usuario'))
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-1">

                    {{-- Menú admin --}}
                    @if(session('usuario')['rol'] === 'superusuario')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/usuarios') ? 'active' : '' }}" href="/admin/usuarios">
                            <i class="bi bi-people me-1"></i>Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/bitacora') ? 'active' : '' }}" href="/admin/bitacora">
                            <i class="bi bi-journal-check me-1"></i>Bitácora
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/sesiones') ? 'active' : '' }}" href="/admin/sesiones">
                            <i class="bi bi-shield-lock me-1"></i>Sesiones
                        </a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('agenda') ? 'active' : '' }}" href="/agenda">
                            <i class="bi bi-book me-1"></i>Mi Agenda
                        </a>
                    </li>
                    @endif

                    {{-- Dropdown usuario --}}
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-light" href="#"
                           id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width:32px;height:32px;font-size:.85rem">
                                {{ strtoupper(substr(session('usuario')['nombre'] ?? 'U', 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline fw-semibold">{{ session('usuario')['nombre'] }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3" aria-labelledby="userDropdown">
                            <li>
                                <div class="dropdown-header">
                                    <p class="fw-bold mb-0 text-dark">{{ session('usuario')['nombre'] }}</p>
                                    <small class="text-muted">{{ session('usuario')['cedula'] }}</small>
                                    <br><span class="badge {{ session('rol') === 'superusuario' ? 'bg-danger' : 'bg-primary' }} mt-1">
                                        {{ ucfirst(session('rol')) }}
                                    </span>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item" href="/perfil">
                                    <i class="bi bi-person-circle me-2 text-muted"></i>Mi Perfil
                                </a>
                            </li>
                            @if(session('rol') === 'superusuario')
                            <li>
                                <a class="dropdown-item" href="/admin/sesiones">
                                    <i class="bi bi-shield-lock me-2 text-muted"></i>Sesiones activas
                                </a>
                            </li>
                            @endif
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="/logout" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
            @endif
        </div>
    </nav>

    <main class="container my-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i><strong>Por favor revise los siguientes errores:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Toast feedback para copiar --}}
    <div id="copyToast" class="toast-copy alert alert-dark text-white shadow-lg rounded-pill px-4 py-2 border-0 small">
        <i class="bi bi-clipboard-check me-2 text-success"></i><span id="copyToastMsg">Teléfono copiado al portapapeles</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Copy helper function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = document.getElementById('copyToast');
                toast.style.display = 'block';
                setTimeout(() => { toast.style.display = 'none'; }, 2000);
            });
        }

        // Auto dismiss alert messages after 5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.querySelectorAll('.alert-dismissible').forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>
