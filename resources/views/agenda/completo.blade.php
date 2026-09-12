<x-app-layout>
    <x-slot name="header">
        @include('agenda.partials.header')
    </x-slot>

    <div class="container-fluid py-4">
        {{-- Leyenda de colores --}}
        <div class="d-flex gap-3 mb-3 flex-wrap align-items-center">
            <span class="badge rounded-pill px-3 py-2" style="background:#f59e0b; font-size:0.82rem;">
                <i class="bi bi-circle-fill me-1"></i> Pendiente
            </span>
            <span class="badge rounded-pill px-3 py-2" style="background:#10b981; font-size:0.82rem;">
                <i class="bi bi-circle-fill me-1"></i> Realizado
            </span>
            <span class="badge rounded-pill px-3 py-2" style="background:#ef4444; font-size:0.82rem;">
                <i class="bi bi-circle-fill me-1"></i> Cancelado
            </span>
            <span class="ms-auto text-muted small align-self-center">
                Haz clic en un evento para ver el detalle
            </span>
        </div>

        {{-- Calendario FullCalendar a pantalla completa --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div id="calendario"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- FullCalendar v6 via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calEl = document.getElementById('calendario');
            var calendar = new FullCalendar.Calendar(calEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                height: 'auto',
                headerToolbar: {
                    left:   'prev,next today',
                    center: 'title',
                    right:  'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today:    'Hoy',
                    month:    'Mes',
                    week:     'Semana',
                    list:     'Lista',
                },
                events: '{{ route("agenda.eventos") }}',
                eventClick: function (info) {
                    // La URL ya viene en el JSON devuelto por eventos()
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.location.href = info.event.url;
                    }
                },
                eventTimeFormat: {
                    hour:   '2-digit',
                    minute: '2-digit',
                    meridiem: false,
                    hour12: false,
                },
            });
            calendar.render();
        });
    </script>
    @endpush
</x-app-layout>
