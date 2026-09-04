@extends('layouts.publico')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                {{-- Encabezado de Sección --}}
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold text-dark mb-2">Buscador Público de Difuntos</h1>
                    <p class="text-muted">Ingrese el nombre o apellidos del difunto para consultar su ubicación registrada.</p>
                </div>

                {{-- Formulario de Búsqueda --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <form action="{{ route('publico.buscar') }}" method="GET" class="row g-2 align-items-center">
                            <div class="col-md-9">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" name="q" value="{{ $query }}" required
                                        class="form-control border-start-0 ps-0 shadow-none"
                                        placeholder="Ingrese nombre, apellidos o código...">
                                </div>
                            </div>
                            <div class="col-md-3 d-grid">
                                <button type="submit" class="btn btn-success btn-lg fw-semibold shadow-sm" style="background-color: #113615; border-color: #113615;">
                                    Buscar Difunto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Resultados de Búsqueda --}}
                @if ($difuntos !== null)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                Resultados de la Búsqueda
                                @if($query !== '')
                                    <small class="text-muted font-normal fs-6">para "{{ $query }}"</small>
                                @endif
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @if ($difuntos->isEmpty())
                                {{-- Mensaje si no hay resultados --}}
                                <div class="p-5 text-center text-muted">
                                    <i class="bi bi-search-heart fs-1 text-secondary opacity-50 d-block mb-3"></i>
                                    <h5 class="fw-semibold text-dark mb-1">No se encontraron resultados.</h5>
                                    <p class="small mb-0">Verifique los datos ingresados o intente buscar con otro nombre o apellido.</p>
                                </div>
                            @else
                                {{-- Listado Seguro: Muestra ÚNICAMENTE el Nombre Completo y Enlace a la Ubicación --}}
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" class="ps-4">Nombre Completo del Difunto</th>
                                                <th scope="col" class="text-end pe-4">Ubicación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($difuntos as $difunto)
                                                <tr>
                                                    <td class="ps-4">
                                                        <div class="fw-semibold text-dark fs-6">
                                                            {{ $difunto->nombre }} {{ $difunto->apellido_paterno }} {{ $difunto->apellido_materno }}
                                                            @if($difunto->apellido_casada)
                                                                <span class="text-muted fw-normal">({{ $difunto->apellido_casada }})</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <a href="{{ route('publico.ficha', $difunto->id) }}" class="btn btn-outline-success btn-sm px-3 rounded-pill">
                                                            <i class="bi bi-geo-alt-fill me-1"></i>Ver ubicación
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                        
                        @if ($difuntos->hasPages())
                            <div class="card-footer bg-white border-0 py-3">
                                {{ $difuntos->links() }}
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
