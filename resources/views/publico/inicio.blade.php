@extends('layouts.publico')

@section('content')
    {{-- Banner Principal (Hero) --}}
    <div class="hero-publico mb-5">
    <div class="container py-5" style="min-height: 480px;">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-lg-9 text-center">

                <h1 class="display-4 fw-bold mb-4" style="color: #113615;">
                    Plataforma Web de Consulta Pública
                </h1>

                <p class="lead mb-5 opacity-90">
                    Consulta la ubicación de difuntos y visualiza el mapa interactivo del Cementerio General de Sacaba.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('publico.buscar') }}" class="btn btn-light btn-lg px-4 fw-semibold shadow-sm text-dark">
                        <i class="bi bi-search me-2 text-success"></i>
                        Buscar Difunto
                    </a>

                    <a href="{{ route('publico.mapa') }}" class="btn btn-outline-light btn-lg px-4 fw-semibold">
                        <i class="bi bi-map me-2"></i>
                        Ver Mapa Interactivo
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

    {{-- Sección de Información Institucional --}}
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                            <div class="rounded-circle p-3 text-white" style="background-color: #113615;">
                                <i class="bi bi-info-circle fs-3"></i>
                            </div>
                            <div>
                                <h3 class="h4 fw-bold mb-0 text-dark">Información Institucional</h3>
                                <small class="text-muted">Cementerio General de Sacaba</small>
                            </div>
                        </div>

                        {{-- Datos institucionales --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold text-dark mb-2">
                                        <i class="bi bi-signpost-2 text-success me-2"></i>Nombre
                                    </h6>
                                    <p class="small text-muted mb-0">
                                        Cementerio General de Sacaba
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold text-dark mb-2">
                                        <i class="bi bi-bank text-success me-2"></i>Entidad Administradora
                                    </h6>
                                    <p class="small text-muted mb-0">
                                        Gobierno Autónomo Municipal de Sacaba (GAMS)
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold text-dark mb-2">
                                        <i class="bi bi-clipboard-check text-success me-2"></i>Objeto de la Plataforma
                                    </h6>
                                    <p class="small text-muted mb-0">
                                        Este espacio permite la consulta digital de registros de inhumaciones, ubicación de nichos, bóvedas y mausoleos, así como el estado de concesiones y trámites administrativos del camposanto.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold text-dark mb-2">
                                        <i class="bi bi-pin-map text-success me-2"></i>Dirección y Ubicación
                                    </h6>
                                    <p class="small text-muted mb-0">
                                        El cementerio está ubicado en la Calle Bolívar, Sacaba, Departamento de Cochabamba, Bolivia, en las cercanías de la Plaza del Cementerio General y la oficina de Administración.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted leading-relaxed">
                            A través de este portal de servicios digitales, los familiares y visitantes pueden consultar de forma rápida la ubicación exacta de sus seres queridos dentro de las instalaciones del cementerio, facilitando su localización y visitas.
                        </p>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light">
                                    <h6 class="fw-bold text-dark mb-2">
                                        <i class="bi bi-search text-success me-2"></i>Consulta Directa
                                    </h6>
                                    <p class="small text-muted mb-0">
                                        Busca registros de sepulturas ingresando nombres o apellidos completos de forma segura.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light">
                                    <h6 class="fw-bold text-dark mb-2">
                                        <i class="bi bi-geo-alt text-success me-2"></i>Ubicación Física
                                    </h6>
                                    <p class="small text-muted mb-0">
                                        Obtén el detalle oficial del bloque, lado, columna, fila, tipo y número de nicho o mausoleo.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
