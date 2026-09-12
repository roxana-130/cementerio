<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="h4 fw-bold text-dark mb-0">
                    <i class="bi bi-pencil-square me-2 text-success"></i>
                    Editar Actividad — {{ $agenda->codigo }}
                </h1>
                <small class="text-muted">Estado actual:
                    @if ($agenda->estado === 'Realizado')
                        <span class="badge bg-success">Realizado</span>
                        — Los campos Difunto, Ubicacion, Tipo y Fecha son de solo lectura
                    @elseif ($agenda->estado === 'Cancelado')
                        <span class="badge bg-danger">Cancelado</span>
                    @else
                        <span class="badge bg-warning text-dark">Pendiente</span>
                    @endif
                </small>
            </div>
            <a href="{{ route('agenda.show', $agenda) }}" class="btn btn-light border">
                <i class="bi bi-arrow-left me-1"></i> Volver al Detalle
            </a>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Hay errores en el formulario:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($agenda->estado === 'Realizado')
                            <div class="alert alert-warning mb-4">
                                <i class="bi bi-lock-fill me-2"></i>
                                <strong>Actividad ya realizada.</strong>
                                Los campos Difunto, Ubicacion, Tipo y Fecha no se pueden modificar.
                                Solo puede editar la Hora, el Panteonero y las Observaciones.
                            </div>
                        @endif

                        <form action="{{ route('agenda.update', $agenda) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">

                                {{-- Tipo --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-dark">Tipo de Actividad</label>
                                    @if ($agenda->estado === 'Realizado')
                                        {{-- Solo lectura: se muestra como texto --}}
                                        <p class="form-control-plaintext fw-semibold">{{ $agenda->tipo }}</p>
                                        <input type="hidden" name="tipo" value="{{ $agenda->tipo }}">
                                    @else
                                        <select name="tipo" id="tipo" required
                                            class="form-select @error('tipo') is-invalid @enderror">
                                            @foreach (['Inhumacion','Exhumacion','Cremacion','Anexion'] as $t)
                                                <option value="{{ $t }}"
                                                    {{ old('tipo', $agenda->tipo) === $t ? 'selected' : '' }}>
                                                    {{ $t }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @endif
                                </div>

                                {{-- Difunto --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-dark">Difunto</label>
                                    @if ($agenda->estado === 'Realizado')
                                        <p class="form-control-plaintext fw-semibold">
                                            {{ $agenda->difunto->apellido_paterno ?? '' }}
                                            {{ $agenda->difunto->apellido_materno ?? '' }}
                                            {{ $agenda->difunto->nombre ?? '—' }}
                                        </p>
                                        <input type="hidden" name="difunto_id" value="{{ $agenda->difunto_id }}">
                                    @else
                                        <select name="difunto_id" id="difunto_id" required
                                            class="form-select @error('difunto_id') is-invalid @enderror">
                                            @foreach ($difuntos as $d)
                                                <option value="{{ $d->id }}"
                                                    {{ old('difunto_id', $agenda->difunto_id) == $d->id ? 'selected' : '' }}>
                                                    {{ $d->apellido_paterno }} {{ $d->apellido_materno }} {{ $d->nombre }} ({{ $d->codigo }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('difunto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @endif
                                </div>

                                {{-- Ubicacion --}}
                                <div class="col-md-6" id="campoUbicacion">
                                    <label class="form-label fw-medium text-dark">
                                        Ubicacion
                                        @if (!in_array($agenda->tipo, ['Cremacion']))
                                            <span class="text-danger">*</span>
                                        @else
                                            <small class="text-muted">(Opcional)</small>
                                        @endif
                                    </label>
                                    @if ($agenda->estado === 'Realizado')
                                        <p class="form-control-plaintext fw-semibold">
                                            @if ($agenda->ubicacion)
                                                {{ $agenda->ubicacion->bloque->nombre ?? '' }} —
                                                {{ $agenda->ubicacion->tipo }} N° {{ $agenda->ubicacion->numero }}
                                            @else
                                                <span class="text-muted">Sin ubicacion asignada</span>
                                            @endif
                                        </p>
                                        <input type="hidden" name="ubicacion_id" value="{{ $agenda->ubicacion_id }}">
                                    @else
                                        <select name="ubicacion_id" id="ubicacion_id"
                                            class="form-select @error('ubicacion_id') is-invalid @enderror">
                                            <option value="">-- Sin ubicacion --</option>
                                            @foreach ($ubicaciones as $u)
                                                <option value="{{ $u->id }}"
                                                    {{ old('ubicacion_id', $agenda->ubicacion_id) == $u->id ? 'selected' : '' }}>
                                                    {{ $u->bloque->nombre ?? 'Sin bloque' }} — {{ $u->tipo }} N° {{ $u->numero }}
                                                    ({{ $u->estado_actual }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('ubicacion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @endif
                                </div>

                                {{-- Panteonero — SIEMPRE editable --}}
                                <div class="col-md-6">
                                    <label for="panteonero_id" class="form-label fw-medium text-dark">
                                        Panteonero <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <select name="panteonero_id" id="panteonero_id"
                                        class="form-select @error('panteonero_id') is-invalid @enderror">
                                        <option value="">-- Sin asignar --</option>
                                        @foreach ($panteoneros as $p)
                                            <option value="{{ $p->id }}"
                                                {{ old('panteonero_id', $agenda->panteonero_id) == $p->id ? 'selected' : '' }}>
                                                {{ $p->apellido_paterno }} {{ $p->apellido_materno }} {{ $p->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('panteonero_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Fecha --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-dark">Fecha</label>
                                    @if ($agenda->estado === 'Realizado')
                                        <p class="form-control-plaintext fw-semibold">
                                            {{ $agenda->fecha->format('d/m/Y') }}
                                        </p>
                                        <input type="hidden" name="fecha" value="{{ $agenda->fecha->format('Y-m-d') }}">
                                    @else
                                        <input type="date" name="fecha" id="fecha"
                                            value="{{ old('fecha', $agenda->fecha->format('Y-m-d')) }}" required
                                            class="form-control @error('fecha') is-invalid @enderror">
                                        @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @endif
                                </div>

                                {{-- Hora — SIEMPRE editable --}}
                                <div class="col-md-4">
                                    <label for="hora" class="form-label fw-medium text-dark">
                                        Hora <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" name="hora" id="hora"
                                        value="{{ old('hora', substr($agenda->hora, 0, 5)) }}" required
                                        class="form-control @error('hora') is-invalid @enderror">
                                    @error('hora') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Observaciones — SIEMPRE editable --}}
                                <div class="col-12">
                                    <label for="observaciones" class="form-label fw-medium text-dark">
                                        Observaciones <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <textarea name="observaciones" id="observaciones" rows="3"
                                        class="form-control @error('observaciones') is-invalid @enderror"
                                        placeholder="Detalles adicionales...">{{ old('observaciones', $agenda->observaciones) }}</textarea>
                                    @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                            </div>

                            <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                                <a href="{{ route('agenda.show', $agenda) }}" class="btn btn-light border px-4">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-success px-4 shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>