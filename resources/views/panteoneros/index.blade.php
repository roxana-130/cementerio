<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 fw-bold text-dark">
                <i class="bi bi-person-gear text-success me-2"></i>Gestión de Panteoneros
            </h1>
            <a href="{{ route('panteoneros.create') }}" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-person-plus-fill"></i>
                <span>Registrar Panteonero</span>
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

    {{-- Buscador y Filtros --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('panteoneros.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-6 col-lg-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                            class="form-control bg-light border-start-0 ps-0"
                            placeholder="Buscar por nombre, apellido paterno, apellido materno o CI...">
                    </div>
                </div>

                <div class="col-md-3 col-lg-3">
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox" name="mostrar_inactivos" value="1" id="mostrar_inactivos"
                            {{ request('mostrar_inactivos') ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label small fw-medium text-muted" for="mostrar_inactivos">
                            Mostrar inactivos también
                        </label>
                    </div>
                </div>

                <div class="col-md-3 col-lg-2 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-funnel-fill"></i> Filtrar
                    </button>
                    @if (request('buscar') || request('mostrar_inactivos'))
                        <a href="{{ route('panteoneros.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Panteoneros --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="card-title mb-0 fw-semibold text-dark">Listado de Panteoneros</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Nombre Completo</th>
                            <th scope="col">CI</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($panteoneros as $panteonero)
                            <tr>
                                <td class="ps-4 font-medium text-dark fw-semibold">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($panteonero->nombre, 0, 1)) }}
                                        </div>
                                        <span>{{ $panteonero->nombre }} {{ $panteonero->apellido_paterno }} {{ $panteonero->apellido_materno }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium">{{ $panteonero->ci }}</span>
                                </td>
                                <td>
                                    @if ($panteonero->activo)
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
                                        <a href="{{ route('panteoneros.edit', $panteonero) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Editar</span>
                                        </a>

                                        <form action="{{ route('panteoneros.toggle-activo', $panteonero) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm {{ $panteonero->activo ? 'btn-outline-danger' : 'btn-outline-success' }} d-flex align-items-center gap-1"
                                                @if ($panteonero->activo) onclick="return confirm('¿Está seguro de desactivar a este panteonero?');" @endif>
                                                <i class="bi {{ $panteonero->activo ? 'bi-person-x-fill' : 'bi-person-check-fill' }}"></i>
                                                <span>{{ $panteonero->activo ? 'Desactivar' : 'Activar' }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No se encontraron panteoneros registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($panteoneros->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $panteoneros->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

