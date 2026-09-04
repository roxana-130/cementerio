<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0 fw-bold text-dark">
            <i class="bi bi-speedometer2 text-success me-2"></i>Dashboard Principal
        </h1>
    </x-slot>

    {{-- Banner de Bienvenida --}}
    <div class="card border-0 shadow-sm mb-4 text-white" style="background: linear-gradient(135deg, #113615 0%, #1e5924 100%);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="h3 fw-bold mb-2">¡Bienvenido(a), {{ Auth::user()->name }}!</h2>
                    <p class="mb-0 opacity-90">
                        Sistema de Gestión del Cementerio General de Sacaba. Has iniciado sesión con el rol de 
                        <span class="badge bg-light text-dark fw-bold text-uppercase px-2.5 py-1">{{ Auth::user()->rol }}</span>.
                    </p>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <i class="bi bi-building-check opacity-25" style="font-size: 5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas de Estadísticas --}}
    <div class="row g-3 mb-4">
        {{-- Total Ubicaciones --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 bg-success-subtle text-success p-3 me-3">
                        <i class="bi bi-grid-3x3-gap-fill fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Ubicaciones Reales</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ \App\Models\Ubicacion::count() }}</h3>
                        <small class="text-muted">Bloque 18 / Lado Norte</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nichos Disponibles --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 bg-primary-subtle text-primary p-3 me-3">
                        <i class="bi bi-check-circle-fill fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Disponibles</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ \App\Models\Ubicacion::count() }}</h3>
                        <small class="text-success fw-medium">100% listos</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Difuntos Registrados --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 bg-warning-subtle text-warning p-3 me-3">
                        <i class="bi bi-person-lines-fill fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Difuntos</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ \App\Models\Difunto::count() }}</h3>
                        <small class="text-muted">Registrados</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Agendas Hoy --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 bg-info-subtle text-info p-3 me-3">
                        <i class="bi bi-calendar-event-fill fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Agenda de Hoy</span>
                        <h3 class="mb-0 fw-bold text-dark">0</h3>
                        <small class="text-muted">Actividades programadas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Accesos Rápidos --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 px-4">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-lightning-charge text-warning me-2"></i>Accesos Rápidos
                    </h5>
                </div>
                <div class="card-body px-4">
                    <div class="list-group list-group-flush">
                        @if (Auth::user()->rol === 'administrador')
                            <a href="{{ route('usuarios.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div>
                                    <h6 class="mb-0 fw-semibold text-dark">Gestión de Usuarios</h6>
                                    <small class="text-muted">Crear, editar y administrar usuarios y roles del sistema.</small>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                        @endif

                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 border-bottom opacity-75">
                            <div>
                                <h6 class="mb-0 fw-semibold text-dark">Mapa Interactivo</h6>
                                <small class="text-muted">Consultar y localizar ubicaciones del cementerio (Próximamente).</small>
                            </div>
                            <span class="badge bg-secondary">Próximamente</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 px-4">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-info-circle text-success me-2"></i>Información del Sistema
                    </h5>
                </div>
                <div class="card-body px-4">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <div>
                                <strong>Plataforma Web Oficial:</strong> Cementerio General de Sacaba.
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <div>
                                <strong>Estructura Física:</strong> Carga inicial verificada del Bloque 18 (Lado Norte: 130 nichos).
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-shield-check text-success mt-1"></i>
                            <div>
                                <strong>Control de Accesos:</strong> Restricciones activadas para el rol <code>{{ Auth::user()->rol }}</code>.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
