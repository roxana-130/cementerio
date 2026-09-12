<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Cementerio General de Sacaba') }} — Consulta Pública</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos de la Interfaz Pública -->
    <style>
        :root {
            --bg-sacaba: #113615;
            --bg-sacaba-dark: #0a240d;
            --bg-sacaba-hover: #1e5924;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8faf9;
            color: #212529;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-publica {
            background-color: var(--bg-sacaba);
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .navbar-publica .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .navbar-publica .nav-link:hover,
        .navbar-publica .nav-link.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .hero-publico {
            background: url('/images/cementerio.jpeg');
            color: #ffffff;
            padding: 3.5rem 1rem;
            text-align: center;
        }

        .footer-publico {
            background-color: #0a240d;
            color: #ffffff;
            padding: 1.5rem 0;
            margin-top: auto;
        }
    </style>

    @stack('styles')
</head>
<body>
    {{-- Header / Navbar Pública --}}
    <nav class="navbar navbar-expand-lg navbar-dark navbar-publica sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-white" href="{{ route('publico.inicio') }}">
                <div class="bg-white text-emerald rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px; color: #113615;">
                    <i class="bi bi-building-fill fs-5"></i>
                </div>
                <div class="lh-sm">
                    <span class="d-block fs-6">Cementerio General</span>
                    <small class="text-success-light opacity-75 fw-normal" style="font-size: 0.72rem;">Sacaba - Cochabamba</small>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublicaContent" aria-controls="navbarPublicaContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarPublicaContent">
                <ul class="navbar-menu navbar-nav ms-auto mb-2 mb-lg-0 gap-1 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('publico.inicio') ? 'active' : '' }}" href="{{ route('publico.inicio') }}">
                            <i class="bi bi-house-door me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('publico.buscar') ? 'active' : '' }}" href="{{ route('publico.buscar') }}">
                            <i class="bi bi-search me-1"></i>Buscar Difunto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('publico.mapa') ? 'active' : '' }}" href="{{ route('publico.mapa') }}">
                            <i class="bi bi-map me-1"></i>Mapa Interactivo
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a class="btn btn-outline-light btn-sm px-3 rounded-pill" href="{{ route('login') }}">
                            <i class="bi bi-shield-lock me-1"></i>Acceso Administrativo
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Contenido Principal --}}
    <main class="flex-grow-1">
        @yield('content')
    </main>

    {{-- Footer Público --}}
    <footer class="footer-publico text-center">
        <div class="container">
            <p class="mb-1 small opacity-75">&copy; {{ date('Y') }} Cementerio General de Sacaba — Gobierno Autónomo Municipal de Sacaba</p>
            <small class="text-white-50" style="font-size: 0.75rem;">Servicios Digitales y Consulta Pública de Espacios Funerarios</small>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
