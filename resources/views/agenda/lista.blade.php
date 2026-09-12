<x-app-layout>
    <x-slot name="header">
        @include('agenda.partials.header')
    </x-slot>

    <div class="container-fluid py-4">

        {{-- Mensajes de exito o error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Buscador y filtros --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form action="{{ route('agenda.lista') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="buscar" value="{{ request('buscar') }}"
                                class="form-control border-start-0 ps-0"
                                placeholder="Buscar por código o nombre del difunto...">
                        </div>
                    </div>

                    <div class="col-md-3 col-lg-2">
                        <select name="tipo" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Todos los tipos --</option>
                            <option value="Inhumacion" {{ request('tipo') === 'Inhumacion' ? 'selected' : '' }}>Inhumación</option>
                            <option value="Exhumacion" {{ request('tipo') === 'Exhumacion' ? 'selected' : '' }}>Exhumación</option>
                            <option value="Cremacion" {{ request('tipo') === 'Cremacion' ? 'selected' : '' }}>Cremación</option>
                            <option value="Anexion" {{ request('tipo') === 'Anexion' ? 'selected' : '' }}>Anexión</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-lg-2">
                        <select name="estado" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Todos los estados --</option>
                            <option value="Pendiente" {{ request('estado') === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="Realizado" {{ request('estado') === 'Realizado' ? 'selected' : '' }}>Realizado</option>
                            <option value="Cancelado" {{ request('estado') === 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <div class="col-md-1 col-lg-1 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                            Buscar
                        </button>
                    </div>

                    @if (request('buscar') || request('tipo') || request('estado'))
                        <div class="col-auto">
                            <a href="{{ route('agenda.lista') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar filtro">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Tabla de Actividades --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title mb-0 fw-semibold text-dark">Listado de Actividades</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-4">Fecha</th>
                                <th scope="col">Hora</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Difunto</th>
                                <th scope="col">Estado</th>
                                <th scope="col" class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($agendas as $agenda)
                                <tr>
                                    <td class="ps-4">
                                        <i class="bi bi-calendar-event text-muted me-1"></i>{{ $agenda->fecha->format('d/m/Y') }}
                                    </td>
                                    <td>{{ substr($agenda->hora, 0, 5) }}</td>
                                    <td><x-badge-tipo-agenda :tipo="$agenda->tipo" /></td>
                                    <td>
                                        @if ($agenda->difunto)
                                            <span class="fw-semibold text-dark">
                                                {{ $agenda->difunto->nombre }} {{ $agenda->difunto->apellido_paterno }}
                                            </span>
                                            <small class="text-muted d-block">{{ $agenda->difunto->codigo }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><x-badge-estado-agenda :estado="$agenda->estado" /></td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('agenda.show', $agenda) }}" class="btn btn-outline-info btn-sm d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-eye"></i>
                                            <span>Ver</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                        No se encontraron actividades de agenda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($agendas->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $agendas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
