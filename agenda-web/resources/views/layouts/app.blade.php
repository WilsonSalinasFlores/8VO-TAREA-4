<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Virtual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/agenda"><i class="bi bi-journal-bookmark-fill me-2"></i>Agenda Virtual</a>
            @if(session('usuario'))
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    @if(session('usuario')['rol'] === 'superusuario')
                    <li class="nav-item"><a class="nav-link" href="/admin/usuarios">Usuarios</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/bitacora">Bitácora</a></li>
                    @endif
                    <li class="nav-item me-3 text-light">
                        <i class="bi bi-person-circle me-1"></i> {{ session('usuario')['nombre'] }}
                    </li>
                    <li class="nav-item">
                        <form action="/logout" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm rounded-pill">
                                <i class="bi bi-box-arrow-right"></i> Salir
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </nav>

    <main class="container my-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @if(session('error') || $errors->any())
    <script>
        @if(session('error'))
        console.error('[Agenda] Error de sesión:', @json(session('error')));
        @endif
        @if($errors->any())
        console.error('[Agenda] Errores de validación:', @json($errors->toArray()));
        @endif
        @if(session('api_debug'))
        console.warn('[Agenda] Respuesta API:', @json(session('api_debug')));
        @endif
    </script>
    @endif
</body>
</html>
