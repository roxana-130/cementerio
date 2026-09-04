<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cementerio General de Sacaba') }}</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos personalizados del Layout -->
    <style>
        :root {
            --bg-sacaba: #113615;
            --bg-sacaba-dark: #0a240d;
            --bg-sacaba-hover: #18481d;
            --bg-sacaba-active: #1e5924;
            --text-success-light: #a3e635;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f4f6f8;
            color: #212529;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: var(--bg-sacaba);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1040;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .bg-sacaba {
            background-color: var(--bg-sacaba) !important;
        }

        .bg-emerald-active {
            background-color: var(--bg-sacaba-active) !important;
            border-left: 4px solid var(--text-success-light);
        }

        .bg-emerald-badge {
            background-color: rgba(163, 230, 53, 0.2);
            color: var(--text-success-light);
            border: 1px solid rgba(163, 230, 53, 0.4);
        }

        .hover-bg:hover {
            background-color: var(--bg-sacaba-hover) !important;
        }

        .text-success-light {
            color: var(--text-success-light) !important;
        }

        /* Content Wrapper */
        .main-wrapper {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Top Navbar secundaria */
        .top-navbar {
            height: 64px;
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        /* Responsive Breakpoints for Mobile Sidebar */
        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -260px;
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex min-vh-100">
        
        {{-- Sidebar Lateral Izquierdo --}}
        @include('layouts.sidebar')

        {{-- Wrapper principal a la derecha del sidebar --}}
        <div class="main-wrapper">
            
            {{-- Top Navbar secundaria (solo para usuario, toggle móvil y notificaciones) --}}
            <header class="top-navbar d-flex align-items-center justify-content-between px-3 px-md-4 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    {{-- Botón Toggle para pantallas móviles --}}
                    <button class="btn btn-outline-secondary d-lg-none p-1 px-2 border-0" type="button" onclick="toggleSidebar()" aria-label="Toggle Navigation">
                        <i class="bi bi-list fs-3"></i>
                    </button>

                    {{-- Título de la página o migas de pan --}}
                    @isset($header)
                        <div>
                            {{ $header }}
                        </div>
                    @else
                        <h1 class="h5 mb-0 fw-semibold text-dark">Cementerio General de Sacaba</h1>
                    @endisset
                </div>

                {{-- Menú secundario derecho: Perfil y Logout --}}
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2 border shadow-sm px-3 py-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-6 text-success"></i>
                            <span class="fw-medium text-dark">{{ Auth::user()->name }}</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill text-capitalize">{{ Auth::user()->rol }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li>
                                <div class="px-3 py-2 border-bottom">
                                    <p class="mb-0 fw-semibold text-dark small">{{ Auth::user()->name }}</p>
                                    <p class="mb-0 text-muted small">{{ Auth::user()->email }}</p>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-menu-item dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person text-muted"></i>
                                    <span>Mi Perfil</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                        <i class="bi bi-box-arrow-right"></i>
                                        <span>Cerrar sesión</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            {{-- Contenido principal de la página --}}
            <main class="flex-grow-1 p-3 p-md-4">
                <div class="container-fluid p-0">
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </main>

            {{-- Footer sencillo --}}
            <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
                <div class="container-fluid">
                    <span>Plataforma Web para la Gestión del Cementerio General de Sacaba &copy; {{ date('Y') }}</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarNav');
            sidebar.classList.toggle('show');
        }
    </script>
</body>
</html>
