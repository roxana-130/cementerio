{{-- Sidebar Lateral Izquierdo --}}
<div class="sidebar bg-sacaba text-white flex-shrink-0 p-3 flex-column" id="sidebarNav">
    {{-- Header del Sidebar / Brand --}}
    <div class="sidebar-header d-flex align-items-center gap-2 pb-3 mb-3 border-bottom border-success-subtle px-2">
        <div class="brand-icon bg-white text-emerald rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px; min-width: 40px; color: #113615;">
            <i class="bi bi-building-fill fs-5"></i>
        </div>
        <div class="lh-sm">
            <span class="fw-bold d-block text-white tracking-wide" style="font-size: 0.95rem;">Cementerio General</span>
            <small class="text-success-light opacity-75 text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Sacaba - Cochabamba</small>
        </div>
    </div>

    {{-- Menu de navegación principal --}}
    <ul class="nav nav-pills flex-column mb-auto gap-1">
        {{-- Dashboard --}}
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active bg-emerald-active fw-semibold' : 'opacity-85 hover-bg' }}">
                <i class="bi bi-speedometer2 me-2 fs-5"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Difuntos --}}
        <li class="nav-item">
            <a href="#" class="nav-link text-white opacity-85 hover-bg">
                <i class="bi bi-person-lines-fill me-2 fs-5"></i>
                <span>Difuntos</span>
            </a>
        </li>

        {{-- Agenda --}}
        <li class="nav-item">
            <a href="#" class="nav-link text-white opacity-85 hover-bg">
                <i class="bi bi-calendar3 me-2 fs-5"></i>
                <span>Agenda</span>
            </a>
        </li>

        {{-- Mapa --}}
        <li class="nav-item">
            <a href="#" class="nav-link text-white opacity-85 hover-bg">
                <i class="bi bi-map-fill me-2 fs-5"></i>
                <span>Mapa</span>
            </a>
        </li>

        {{-- Módulos restringidos a Administrador --}}
        @if (Auth::user() && Auth::user()->rol === 'administrador')
            <li class="nav-header text-uppercase text-success-light fw-bold mt-3 mb-1 px-3 opacity-75" style="font-size: 0.7rem; letter-spacing: 1px;">
                Administración
            </li>

            {{-- Historial --}}
            <li class="nav-item">
                <a href="#" class="nav-link text-white opacity-85 hover-bg">
                    <i class="bi bi-clock-history me-2 fs-5"></i>
                    <span>Historial</span>
                </a>
            </li>

            {{-- Panteoneros --}}
            <li class="nav-item">
                <a href="#" class="nav-link text-white opacity-85 hover-bg">
                    <i class="bi bi-person-gear me-2 fs-5"></i>
                    <span>Panteoneros</span>
                </a>
            </li>

            {{-- Usuarios --}}
            <li class="nav-item">
                <a href="{{ route('usuarios.index') }}" class="nav-link text-white {{ request()->routeIs('usuarios.*') ? 'active bg-emerald-active fw-semibold' : 'opacity-85 hover-bg' }}">
                    <i class="bi bi-people-fill me-2 fs-5"></i>
                    <span>Usuarios</span>
                </a>
            </li>
        @endif
    </ul>

    {{-- Footer del Sidebar / Usuario logueado --}}
    <div class="sidebar-footer pt-3 mt-auto border-top border-success-subtle px-2">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <div class="avatar bg-white-10 rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; min-width: 35px; background: rgba(255,255,255,0.15);">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="lh-xs overflow-hidden">
                    <span class="d-block text-truncate fw-semibold text-white" style="font-size: 0.85rem;">{{ Auth::user()->name ?? 'Usuario' }}</span>
                    <span class="badge bg-emerald-badge text-uppercase" style="font-size: 0.65rem;">{{ Auth::user()->rol ?? 'personal' }}</span>
                </div>
            </div>
            
            {{-- Formulario de Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-link text-white opacity-75 hover-opacity p-1 text-decoration-none" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </button>
            </form>
        </div>
    </div>
</div>
