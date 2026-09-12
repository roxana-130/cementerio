<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 fw-bold text-dark">
                <i class="bi bi-person-lines-fill text-success me-2"></i>Gestión de Difuntos
            </h1>
            <a href="{{ route('difuntos.create') }}" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-person-plus-fill"></i>
                <span>Registrar Difunto</span>
            </a>
        </div>
    </x-slot>

    {{-- Alertas de éxito o error --}}
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

    {{-- Card de filtro y búsqueda --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('difuntos.index') }}" method="GET" class="row g-3 align-items-center">
                {{-- Campo de búsqueda por texto --}}
                <div class="col-md-6 col-lg-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="buscar" value="{{ $buscar ?? '' }}" 
                            class="form-control border-start-0 ps-0" 
                            placeholder="Buscar por código, CI, nombre o apellidos...">
                    </div>
                </div>

                {{-- Checkbox de incluir inactivos --}}
                <div class="col-md-3 col-lg-3">
                    <div class="form-check form-switch pt-2">
                        <input class="form-check-input" type="checkbox" name="incluir_inactivos" id="incluir_inactivos" value="1" 
                            {{ !empty($incluirInactivos) ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label small fw-medium text-secondary" for="incluir_inactivos">
                            Incluir deshabilitados / inactivos
                        </label>
                    </div>
                </div>

                {{-- Botones de enviar y limpiar --}}
                <div class="col-md-3 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        Buscar
                    </button>
                    @if(!empty($buscar) || !empty($incluirInactivos))
                        <a href="{{ route('difuntos.index') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar filtro">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Difuntos --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="card-title mb-0 fw-semibold text-dark">Listado de Difuntos</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Código</th>
                            <th scope="col">Nombre Completo</th>
                            <th scope="col">Fecha Fallecimiento</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($difuntos as $difunto)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-success">
                                    {{ $difunto->codigo }}
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        {{ $difunto->nombre }} {{ $difunto->apellido_paterno }} {{ $difunto->apellido_materno }}
                                        @if($difunto->apellido_casada)
                                            <span class="text-muted font-normal">({{ $difunto->apellido_casada }})</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ $difunto->fecha_fallecimiento ? $difunto->fecha_fallecimiento->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($difunto->activo)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        {{-- Ver Ficha --}}
                                        <a href="{{ route('difuntos.show', $difunto) }}" class="btn btn-outline-info btn-sm d-flex align-items-center gap-1" title="Ver Ficha">
                                            <i class="bi bi-eye"></i>
                                            <span>Ver</span>
                                        </a>

                                        {{-- Editar --}}
                                        <a href="{{ route('difuntos.edit', $difunto) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Editar</span>
                                        </a>

                                        {{-- Activar / Desactivar con confirm JS --}}
                                        <form action="{{ route('difuntos.toggle-activo', $difunto) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('¿Está seguro de {{ $difunto->activo ? 'desactivar' : 'activar' }} a este difunto ({{ $difunto->nombre }} {{ $difunto->apellido_paterno }})?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $difunto->activo ? 'btn-outline-danger' : 'btn-outline-success' }} d-flex align-items-center gap-1">
                                                <i class="bi {{ $difunto->activo ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                                <span>{{ $difunto->activo ? 'Desactivar' : 'Activar' }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                    No se encontraron registros de difuntos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($difuntos->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $difuntos->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
