@extends('layouts.publico')

@push('styles')
    <!-- Leaflet 1.9.4 CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #mapaPublico {
            width: 100%;
            height: 550px;
            border-radius: 12px;
            z-index: 1;
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
                        <h1 class="h3 fw-bold text-dark mb-1">Mapa Interactivo del Cementerio</h1>
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

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Leaflet 1.9.4 JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Coordenadas generales del Cementerio General de Sacaba, Cochabamba
            const sacabaLat = -17.4041;
            const sacabaLng = -66.0405;
            const zoomLevel = 16;

            // Inicialización del mapa base con Leaflet
            const map = L.map('mapaPublico').setView([sacabaLat, sacabaLng], zoomLevel);

            // Capa de mosaicos OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | Cementerio General de Sacaba'
            }).addTo(map);

            // TODO: cargar marcadores reales cuando existan coordenadas geo en la tabla ubicaciones (pendiente del Módulo 3 - Mapa).
        });
    </script>
@endpush
