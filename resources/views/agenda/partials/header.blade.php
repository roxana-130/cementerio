@php
    $enCalendario = request()->routeIs('agenda.index') || request()->routeIs('agenda.completo');
    $enLista      = request()->routeIs('agenda.lista');
@endphp

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1 class="h4 fw-bold text-dark mb-2">
            <i class="bi bi-calendar3 me-2 text-success"></i>Agenda / Calendario
        </h1>
        <div class="btn-group" role="group" aria-label="Cambiar vista de Agenda">
            <a href="{{ route('agenda.index') }}"
                class="btn btn-sm {{ $enCalendario ? 'btn-success' : 'btn-outline-success' }}">
                <i class="bi bi-calendar3 me-1"></i>Calendario
            </a>
            <a href="{{ route('agenda.lista') }}"
                class="btn btn-sm {{ $enLista ? 'btn-success' : 'btn-outline-success' }}">
                <i class="bi bi-list-ul me-1"></i>Lista
            </a>
        </div>
    </div>
</div>
{{-- La creacion de actividades ya no tiene una pagina propia: se hace
     desde una celda vacia del grid en la vista Semana (dentro de Calendario). --}}
