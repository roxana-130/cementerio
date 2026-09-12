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

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap 5 + AdminLTE 4 + paleta institucional (compilados por Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg">
    <div class="app-wrapper">

        {{-- Sidebar Lateral Izquierdo (AdminLTE) --}}
        @include('layouts.sidebar')

        {{-- Header / Navbar superior --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Mostrar u ocultar el menú lateral">
                            <i class="bi bi-list fs-4"></i>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-5 text-success"></i>
                            <span class="fw-medium text-dark d-none d-sm-inline">{{ Auth::user()->name }}</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill text-capitalize d-none d-sm-inline">{{ Auth::user()->rol }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li>
                                <div class="px-3 py-2 border-bottom">
                                    <p class="mb-0 fw-semibold text-dark small">{{ Auth::user()->name }}</p>
                                    <p class="mb-0 text-muted small">{{ Auth::user()->email }}</p>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('profile.edit') }}">
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
                    </li>
                </ul>
            </div>
        </nav>

        {{-- Contenido principal --}}
        <main class="app-main">
            @isset($header)
                <div class="app-content-header">
                    <div class="container-fluid">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <div class="app-content">
                <div class="container-fluid">
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="app-footer text-center text-muted small">
            <span>Plataforma Web para la Gestión del Cementerio General de Sacaba &copy; {{ date('Y') }}</span>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
