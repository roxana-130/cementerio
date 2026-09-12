<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('difuntos.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-1 px-2" title="Volver al listado">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="h4 mb-0 fw-bold text-dark">
                    Ficha de Detalle del Difunto: <span class="font-monospace text-success">{{ $difunto->codigo }}</span>
                </h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('difuntos.edit', $difunto) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-pencil-square"></i>
                    <span>Editar</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row g-4">
        {{-- Columna Izquierda: Información General del Difunto --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-person-vcard text-success me-2"></i>Datos Personales
                    </h5>
                    @if ($difunto->activo)
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                            <i class="bi bi-check-circle-fill me-1"></i>Activo
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">
                            <i class="bi bi-x-circle-fill me-1"></i>Inactivo / Desactivado
                        </span>
                    @endif
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Código Interno</span>
                            <span class="fw-bold font-monospace text-dark fs-6">{{ $difunto->codigo }}</span>
                        </div>

                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Carnet de Identidad (CI)</span>
                            <span class="fw-bold text-dark">{{ $difunto->ci }}</span>
                        </div>

                        <div class="col-12 border-top pt-2">
                            <span class="text-muted small d-block">Nombre Completo</span>
                            <span class="fw-bold text-dark fs-5">
                                {{ $difunto->nombre }} {{ $difunto->apellido_paterno }} {{ $difunto->apellido_materno }}
                                @if($difunto->apellido_casada)
                                    <span class="text-muted fs-6">({{ $difunto->apellido_casada }})</span>
                                @endif
                            </span>
                        </div>

                        <div class="col-sm-6 border-top pt-2">
                            <span class="text-muted small d-block">Edad al Fallecer</span>
                            <span class="fw-semibold text-dark">{{ $difunto->edad ? $difunto->edad . ' años' : 'No registrada' }}</span>
                        </div>

                        <div class="col-sm-6 border-top pt-2">
                            <span class="text-muted small d-block">Profesión u Ocupación</span>
                            <span class="fw-semibold text-dark">{{ $difunto->profesion_ocupacion ?? 'No registrada' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-journal-medical text-success me-2"></i>Información de Defunción
                    </h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Fecha de Fallecimiento</span>
                            <span class="fw-semibold text-dark">
                                <i class="bi bi-calendar-event text-secondary me-1"></i>
                                {{ $difunto->fecha_fallecimiento ? $difunto->fecha_fallecimiento->format('d/m/Y') : 'No registrada' }}
                            </span>
                        </div>

                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Hora de Fallecimiento</span>
                            <span class="fw-semibold text-dark">
                                <i class="bi bi-clock text-secondary me-1"></i>
                                {{ $difunto->hora_fallecimiento ? \Illuminate\Support\Carbon::parse($difunto->hora_fallecimiento)->format('H:i') : 'No registrada' }}
                            </span>
                        </div>

                        <div class="col-12 border-top pt-2">
                            <span class="text-muted small d-block">Número de Certificado de Defunción</span>
                            <span class="fw-semibold text-dark">{{ $difunto->numero_certificado_defuncion ?? 'No registrado' }}</span>
                        </div>

                        <div class="col-12 border-top pt-2">
                            <span class="text-muted small d-block">Causa de Muerte</span>
                            <p class="mb-0 text-dark">{{ $difunto->causa_muerte ?? 'No especificada' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Ubicación y Agenda (Actividades) --}}
        <div class="col-lg-5">
            {{-- Ubicación Actual --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Ubicación Actual
                    </h5>
                </div>
                <div class="card-body pt-0">
                    @if ($ubicacionActiva)
                        <div class="bg-light p-3 rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary text-uppercase">{{ $ubicacionActiva->tipo }}</span>
                                <span class="small text-muted font-monospace">N° {{ $ubicacionActiva->numero }}</span>
                            </div>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1"><strong>Bloque:</strong> {{ $ubicacionActiva->bloque->codigo ?? 'N/A' }} - {{ $ubicacionActiva->bloque->nombre ?? 'Bloque' }}</li>
                                <li class="mb-1"><strong>Lado:</strong> {{ $ubicacionActiva->lado }}</li>
                                <li class="mb-1"><strong>Columna:</strong> {{ $ubicacionActiva->columna }} | <strong>Fila:</strong> {{ $ubicacionActiva->fila }}</li>
                                <li>
                                    <strong>Fecha de Ingreso:</strong> 
                                    {{ $ubicacionActiva->pivot->fecha_ingreso ? \Illuminate\Support\Carbon::parse($ubicacionActiva->pivot->fecha_ingreso)->format('d/m/Y') : '-' }}
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="p-3 text-center bg-light rounded text-muted border">
                            <i class="bi bi-geo text-secondary fs-4 d-block mb-1"></i>
                            <span class="fw-medium">Sin ubicación asignada actualmente.</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actividades Relacionadas --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar3 text-success me-2"></i>Actividades Relacionadas
                    </h5>
                </div>
                <div class="card-body pt-0">
                    {{-- TODO: cargar actividades cuando exista el Módulo 5 - Agenda --}}
                    <div class="p-3 bg-light rounded border text-muted">
                        <p class="mb-0 small">
                            <i class="bi bi-info-circle me-1"></i>
                            No hay actividades registradas aún. Las actividades del difunto (inhumación, exhumación, cremación) se visualizarán en este bloque una vez implementado el Módulo 5 (Agenda).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
