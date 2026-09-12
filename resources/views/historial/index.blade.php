<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 fw-bold text-dark">
                <i class="bi bi-clock-history text-success me-2"></i>Historial de Auditoría
            </h1>
            <span class="badge bg-light text-muted border px-3 py-2">
                <i class="bi bi-eye-fill me-1"></i>Solo Lectura
            </span>
        </div>
    </x-slot>

    {{-- Filtros de Auditoría --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('historial.index') }}" method="GET" class="row g-3 align-items-end">
                {{-- Filtro por Tabla --}}
                <div class="col-md-3">
                    <label for="tabla" class="form-label small fw-semibold text-muted">Módulo / Tabla</label>
                    <select name="tabla" id="tabla" class="form-select form-select-sm">
                        <option value="">-- Todos los módulos --</option>
                        <option value="difuntos" {{ request('tabla') === 'difuntos' ? 'selected' : '' }}>Difuntos</option>
                        <option value="ubicaciones" {{ request('tabla') === 'ubicaciones' ? 'selected' : '' }}>Ubicaciones</option>
                        <option value="panteoneros" {{ request('tabla') === 'panteoneros' ? 'selected' : '' }}>Panteoneros</option>
                        <option value="usuarios" {{ request('tabla') === 'usuarios' ? 'selected' : '' }}>Usuarios</option>
                    </select>
                </div>

                {{-- Filtro por Usuario --}}
                <div class="col-md-3">
                    <label for="user_id" class="form-label small fw-semibold text-muted">Usuario Responsable</label>
                    <select name="user_id" id="user_id" class="form-select form-select-sm">
                        <option value="">-- Todos los usuarios --</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ (string)request('user_id') === (string)$usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fecha Desde --}}
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label small fw-semibold text-muted">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}"
                        class="form-control form-control-sm">
                </div>

                {{-- Fecha Hasta --}}
                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label small fw-semibold text-muted">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}"
                        class="form-control form-control-sm">
                </div>

                {{-- Botones de Acción --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-funnel-fill"></i> Filtrar
                    </button>
                    @if (request('tabla') || request('user_id') || request('fecha_desde') || request('fecha_hasta'))
                        <a href="{{ route('historial.index') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Registros de Historial --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="card-title mb-0 fw-semibold text-dark">Registro Cronológico de Operaciones</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Fecha y Hora</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Módulo</th>
                            <th scope="col">Acción</th>
                            <th scope="col">Descripción</th>
                            <th scope="col" class="text-end pe-4">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historiales as $item)
                            <tr>
                                {{-- Fecha y Hora --}}
                                <td class="ps-4 text-nowrap">
                                    <span class="fw-semibold text-dark">{{ $item->created_at->format('d/m/Y') }}</span>
                                    <small class="text-muted d-block">{{ $item->created_at->format('H:i:s') }}</small>
                                </td>

                                {{-- Usuario --}}
                                <td>
                                    @if ($item->user)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                                {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="text-dark fw-medium">{{ $item->user->name }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Sistema</span>
                                    @endif
                                </td>

                                {{-- Módulo / Tabla --}}
                                <td>
                                    @php
                                        $badgeBg = match($item->tabla) {
                                            'difuntos' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                            'ubicaciones' => 'bg-warning-subtle text-dark border border-warning-subtle',
                                            'panteoneros' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                            'usuarios' => 'bg-purple-subtle text-purple border border-purple-subtle',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <span class="badge text-uppercase {{ $badgeBg }}" style="font-size: 0.75rem;">
                                        {{ $item->tabla }}
                                    </span>
                                </td>

                                {{-- Acción --}}
                                <td>
                                    @php
                                        $badgeAccion = match($item->accion) {
                                            'Crear', 'Activar' => 'bg-success-subtle text-success border border-success-subtle',
                                            'Editar', 'Asociar' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                            'Desactivar', 'Retirar' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeAccion }}" style="font-size: 0.75rem;">
                                        {{ $item->accion }}
                                    </span>
                                </td>

                                {{-- Descripción --}}
                                <td>
                                    <span class="text-dark">{{ $item->descripcion }}</span>
                                </td>

                                {{-- Detalle (Botón modal) --}}
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#detalleModal-{{ $item->id }}">
                                        <i class="bi bi-info-circle me-1"></i>Ver detalle
                                    </button>

                                    {{-- Modal de Detalle --}}
                                    <div class="modal fade text-start" id="detalleModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h6 class="modal-title fw-bold text-dark">
                                                        <i class="bi bi-journal-text text-success me-2"></i>
                                                        Detalle de Auditoría — Registro #{{ $item->id }}
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    {{-- Datos Resumen --}}
                                                    <div class="row g-2 mb-3 bg-light p-3 rounded">
                                                        <div class="col-sm-6">
                                                            <small class="text-muted d-block">Fecha y Hora:</small>
                                                            <strong>{{ $item->created_at->format('d/m/Y H:i:s') }}</strong>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <small class="text-muted d-block">Usuario:</small>
                                                            <strong>{{ $item->user->name ?? 'Sistema' }}</strong>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <small class="text-muted d-block">Módulo y Registro ID:</small>
                                                            <strong class="text-capitalize">{{ $item->tabla }} (ID: {{ $item->registro_id }})</strong>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <small class="text-muted d-block">Acción:</small>
                                                            <span class="badge {{ $badgeAccion }}">{{ $item->accion }}</span>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <small class="text-muted d-block">Descripción:</small>
                                                            <span>{{ $item->descripcion }}</span>
                                                        </div>
                                                    </div>

                                                    {{-- Comparación de Campos --}}
                                                    @php
                                                        $anteriores = $item->datos_anteriores ?? [];
                                                        $nuevos = $item->datos_nuevos ?? [];
                                                        $todosLosCampos = array_unique(array_merge(array_keys($anteriores), array_keys($nuevos)));
                                                    @endphp

                                                    @if (empty($todosLosCampos))
                                                        <p class="text-muted fst-italic mb-0 text-center py-3">
                                                            No se registraron cambios específicos por campo en esta operación.
                                                        </p>
                                                    @else
                                                        <div class="table-responsive border rounded">
                                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th style="width: 25%;">Campo</th>
                                                                        <th style="width: 37.5%;" class="text-danger">Valor Anterior</th>
                                                                        <th style="width: 37.5%;" class="text-success">Valor Nuevo</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($todosLosCampos as $campo)
                                                                        @php
                                                                            $valAnt = $anteriores[$campo] ?? null;
                                                                            $valNue = $nuevos[$campo] ?? null;

                                                                            $formatVal = function($val) {
                                                                                if (is_bool($val)) {
                                                                                    return $val ? 'true (Sí)' : 'false (No)';
                                                                                }
                                                                                if (is_null($val)) {
                                                                                    return '(vacío)';
                                                                                }
                                                                                if (is_array($val)) {
                                                                                    return json_encode($val, JSON_UNESCAPED_UNICODE);
                                                                                }
                                                                                return (string)$val;
                                                                            };
                                                                        @endphp
                                                                        <tr>
                                                                            <td class="fw-semibold text-muted">{{ $campo }}</td>
                                                                            <td class="text-break {{ is_null($valAnt) ? 'text-muted fst-italic' : '' }}">
                                                                                {{ $formatVal($valAnt) }}
                                                                            </td>
                                                                            <td class="text-break {{ is_null($valNue) ? 'text-muted fst-italic' : '' }}">
                                                                                {{ $formatVal($valNue) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer bg-light py-2">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No se encontraron registros en el historial de auditoría con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($historiales->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $historiales->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
