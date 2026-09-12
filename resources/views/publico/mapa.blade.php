@extends('layouts.publico')

@push('styles')
    <!-- Leaflet 1.9.4 CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- Leaflet Routing Machine 3.2.12 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <style>
        #mapaPublico {
            width: 100%;
            height: 500px;
            border-radius: 12px;
            z-index: 1;
        }

        /* Mas bajo en pantallas chicas, como pide RNF04 (responsive) */
        @media (max-width: 575.98px) {
            #mapaPublico {
                height: 300px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-11">

                {{-- Encabezado --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-1">
                            <i class="bi bi-geo-alt-fill text-success me-1"></i>📍 ¿Dónde nos encontramos?
                        </h1>
                        <p class="text-muted small mb-0">Visualización espacial del Cementerio General de Sacaba</p>
                    </div>
                    <a href="{{ route('publico.buscar') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                        <i class="bi bi-search me-1"></i>Buscar Difunto
                    </a>
                </div>

                {{-- Tarjeta contenedora del mapa --}}
                <div class="card border-0 shadow-sm p-3">
                    <div id="mapaPublico"></div>
                </div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
                    <p class="text-muted small mb-0" id="textoAyudaUbicacion">
                        <i class="bi bi-info-circle me-1"></i>
                        Se muestra una ruta de referencia. Haz clic en el mapa o en "Obtener mi ruta"
                        para ver las indicaciones desde tu ubicación actual.
                    </p>
                    <button type="button" class="btn btn-success" id="btnObtenerRuta">
                        <i class="bi bi-signpost-split me-1"></i>Obtener mi ruta
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Leaflet 1.9.4 JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Leaflet Routing Machine 3.2.12 JS -->
    <script src="https://cdn.jsdelivr.net/npm/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', inicializarMapaUbicacion);

        var LAT_CEMENTERIO = -17.41111097343219;
        var LNG_CEMENTERIO = -66.04123402883565;

        var LAT_REFERENCIA = -17.4041;
        var LNG_REFERENCIA = -66.0405;

        var mapa = null;
        var controlRuta = null;

        function inicializarMapaUbicacion() {
            mapa = L.map('mapaPublico').setView([LAT_CEMENTERIO, LNG_CEMENTERIO], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | Cementerio General de Sacaba'
            }).addTo(mapa);

            // Marcador rojo fijo del cementerio (destino).
            var iconoCementerio = L.divIcon({
                className: '',
                html: '<div style="width:20px;height:20px;background:#dc2626;border:3px solid #7f1d1d;border-radius:50%;"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10],
            });
            L.marker([LAT_CEMENTERIO, LNG_CEMENTERIO], { icon: iconoCementerio })
                .addTo(mapa)
                .bindPopup('Cementerio General de Sacaba')
                .openPopup();

            trazarRuta(LAT_REFERENCIA, LNG_REFERENCIA, 'Punto de referencia (Sacaba)');

            document.getElementById('btnObtenerRuta').addEventListener('click', obtenerMiRuta);

            mapa.on('click', function (evento) {
                trazarRuta(evento.latlng.lat, evento.latlng.lng, 'Punto seleccionado');
                document.getElementById('textoAyudaUbicacion').textContent =
                    'Mostrando la ruta desde el punto que seleccionaste en el mapa.';
            });
        }

        function obtenerMiRuta() {
            var textoAyuda = document.getElementById('textoAyudaUbicacion');

            if (!navigator.geolocation) {
                alert('Tu navegador no soporta geolocalización.');
                return;
            }

            textoAyuda.textContent = 'Obteniendo tu ubicación...';

            navigator.geolocation.getCurrentPosition(
                function (posicion) {
                    trazarRuta(posicion.coords.latitude, posicion.coords.longitude, 'Tu ubicación');
                    textoAyuda.textContent = 'Mostrando la ruta desde tu ubicación actual hasta el cementerio.';
                },
                function () {
                    alert('Debes permitir el acceso a tu ubicación para ver la ruta');
                    textoAyuda.textContent = 'No se pudo obtener tu ubicación. Se sigue mostrando la ruta de referencia.';
                }
            );
        }

        function trazarRuta(latOrigen, lngOrigen, etiquetaOrigen) {
            if (controlRuta) {
                mapa.removeControl(controlRuta);
                controlRuta = null;
            }

            controlRuta = L.Routing.control({
                waypoints: [
                    L.latLng(latOrigen, lngOrigen),
                    L.latLng(LAT_CEMENTERIO, LNG_CEMENTERIO),
                ],
                routeWhileDragging: false,
                addWaypoints: false,
                show: false, // sin el panel de instrucciones paso a paso, solo la linea en el mapa
                lineOptions: { styles: [{ color: '#113615', weight: 5 }] },
                createMarker: function (indice, waypoint) {
                    if (indice === 0) {
                        return L.marker(waypoint.latLng).bindPopup(etiquetaOrigen);
                    }
                    return null; // el marcador del cementerio ya esta puesto aparte
                },
            }).addTo(mapa);
        }
    </script>
@endpush
