@extends('layouts.publico')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                {{-- Botón de retorno --}}
                <div class="mb-3">
                    <a href="{{ route('publico.buscar') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i>Volver a la búsqueda
                    </a>
                </div>

                {{-- Tarjeta de Ficha Pública --}}
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header text-white p-4" style="background-color: #113615;">
                        <span class="badge bg-light text-dark text-uppercase px-2.5 py-1 mb-2 font-semibold">Consulta Pública</span>
                        <h2 class="h3 fw-bold mb-0">
                            {{ $difunto->nombre }} {{ $difunto->apellido_paterno }} {{ $difunto->apellido_materno }}
                            @if($difunto->apellido_casada)
                                <span class="fw-normal opacity-90">({{ $difunto->apellido_casada }})</span>
                            @endif
                        </h2>
                    </div>

                    <div class="card-body p-4 p-md-5 bg-white">
                        <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">
                            <i class="bi bi-geo-alt-fill text-success me-2"></i>Ubicación Registrada
                        </h5>

                        @if ($ubicacion)
                            {{-- Detalle físico oficial de la ubicación --}}
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-4">
                                    <div class="p-3 border rounded-3 bg-light text-center">
                                        <small class="text-muted text-uppercase fw-semibold d-block mb-1">Bloque</small>
                                        <span class="fs-5 fw-bold text-dark">{{ $ubicacion->bloque->nombre ?? $ubicacion->bloque->codigo }}</span>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-4">
                                    <div class="p-3 border rounded-3 bg-light text-center">
                                        <small class="text-muted text-uppercase fw-semibold d-block mb-1">Lado</small>
                                        <span class="fs-5 fw-bold text-dark">{{ $ubicacion->lado }}</span>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-4">
                                    <div class="p-3 border rounded-3 bg-light text-center">
                                        <small class="text-muted text-uppercase fw-semibold d-block mb-1">Tipo de Espacio</small>
                                        <span class="fs-5 fw-bold text-dark">{{ $ubicacion->tipo }}</span>
                                    </div>
                                </div>

                                <div class="col-sm-4 col-md-4">
                                    <div class="p-3 border rounded-3 bg-light text-center">
                                        <small class="text-muted text-uppercase fw-semibold d-block mb-1">Número</small>
                                        <span class="fs-4 fw-bold text-success">Nº {{ $ubicacion->numero }}</span>
                                    </div>
                                </div>

                                <div class="col-sm-4 col-md-4">
                                    <div class="p-3 border rounded-3 bg-light text-center">
                                        <small class="text-muted text-uppercase fw-semibold d-block mb-1">Fila</small>
                                        <span class="fs-5 fw-bold text-dark">{{ $ubicacion->fila }}</span>
                                    </div>
                                </div>

                                <div class="col-sm-4 col-md-4">
                                    <div class="p-3 border rounded-3 bg-light text-center">
                                        <small class="text-muted text-uppercase fw-semibold d-block mb-1">Columna</small>
                                        <span class="fs-5 fw-bold text-dark">{{ $ubicacion->columna }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Mensaje si el difunto no tiene ubicación activa --}}
                            <div class="alert alert-warning border-0 p-4 text-center">
                                <i class="bi bi-info-circle-fill fs-3 text-warning d-block mb-2"></i>
                                <span class="fw-semibold text-dark fs-5">Ubicación no disponible por el momento.</span>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-light border-0 py-3 text-center text-muted small">
                        <i class="bi bi-shield-check me-1"></i>Información física oficial del Cementerio General de Sacaba.
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
