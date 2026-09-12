{{-- Sidebar Lateral Izquierdo (AdminLTE 4) --}}
<aside class="app-sidebar shadow" data-bs-theme="dark">

    {{-- Marca / encabezado del sidebar --}}
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <div class="brand-icon bg-white text-emerald rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0" style="width: 36px; height: 36px; color: #113615;">
                <i class="bi bi-building-fill fs-6"></i>
            </div>
            <span class="brand-text fw-bold ms-2 lh-sm text-white">
                Cementerio General
                <small class="d-block text-success-light text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Sacaba - Cochabamba</small>
            </span>
        </a>
    </div>

    {{-- Menu de navegacion principal --}}
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="sidebar-menu flex-column" role="menu">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- Difuntos --}}
                <li class="nav-item">
                    <a href="{{ route('difuntos.index') }}" class="nav-link {{ request()->routeIs('difuntos.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-lines-fill"></i>
                        <p>Difuntos</p>
                    </a>
                </li>

                {{-- Agenda --}}
                <li class="nav-item">
                    <a href="{{ route('agenda.index') }}" class="nav-link {{ request()->routeIs('agenda.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar3"></i>
                        <p>Agenda</p>
                    </a>
                </li>

                {{-- Mapa --}}
                <li class="nav-item">
                    <a href="{{ route('mapa.index') }}" class="nav-link {{ request()->routeIs('mapa.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-map-fill"></i>
                        <p>Mapa</p>
                    </a>
                </li>

                {{-- Modulos restringidos a Administrador --}}
                @if (Auth::user() && Auth::user()->rol === 'administrador')
                    <li class="nav-header">ADMINISTRACIÓN</li>

                    {{-- Historial --}}
                    <li class="nav-item">
                        <a href="{{ route('historial.index') }}" class="nav-link {{ request()->routeIs('historial.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-clock-history"></i>
                            <p>Historial</p>
                        </a>
                    </li>

                    {{-- Panteoneros --}}
                    <li class="nav-item">
                        <a href="{{ route('panteoneros.index') }}" class="nav-link {{ request()->routeIs('panteoneros.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-person-gear"></i>
                            <p>Panteoneros</p>
                        </a>
                    </li>

                    {{-- Usuarios --}}
                    <li class="nav-item">
                        <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people-fill"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>

    {{-- Footer del Sidebar / Usuario logueado --}}
    <div class="sidebar-footer border-top border-white-10 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <div class="avatar bg-white-10 rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 35px; height: 35px; background: rgba(255,255,255,0.15);">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="lh-sm overflow-hidden">
                    <span class="d-block text-truncate fw-semibold text-white" style="font-size: 0.85rem;">{{ Auth::user()->name ?? 'Usuario' }}</span>
                    <span class="badge bg-emerald-badge text-uppercase" style="font-size: 0.65rem;">{{ Auth::user()->rol ?? 'personal' }}</span>
                </div>
            </div>

            {{-- Formulario de Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-link text-white opacity-75 p-1 text-decoration-none" title="Cerrar sesion">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
