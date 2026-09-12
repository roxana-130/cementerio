<x-app-layout>

    <div class="container-fluid py-4">

        {{-- Mensajes de exito o error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Selector de vista: Mes / Semana --}}
        <div class="btn-group mb-3" role="group" aria-label="Cambiar vista de calendario">
            <button type="button" id="btnVistaMes" class="btn btn-success active">
                <i class="bi bi-calendar3 me-1"></i>Mes
            </button>
            <button type="button" id="btnVistaSemana" class="btn btn-outline-success">
                <i class="bi bi-calendar-week me-1"></i>Semana
            </button>
        </div>

        {{-- ================================================================
             VISTA MES — contenido existente, sin cambios.
             ================================================================ --}}
        <div id="vistaMes">
            <div class="row g-4">

                {{-- Columna izquierda: calendario compacto --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-calendar-week text-success fs-5"></i>
                                <span class="fw-semibold">Calendario del mes</span>
                            </div>

                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                {{-- Leyenda de colores --}}
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge rounded-pill px-2 py-1" style="background:#f59e0b; font-size:0.72rem;">Pendiente</span>
                                    <span class="badge rounded-pill px-2 py-1" style="background:#10b981; font-size:0.72rem;">Realizado</span>
                                    <span class="badge rounded-pill px-2 py-1" style="background:#ef4444; font-size:0.72rem;">Cancelado</span>
                                </div>

                                <a href="{{ route('agenda.completo') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrows-fullscreen me-1"></i>Ver calendario completo
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div id="mini-calendario"></div>
                        </div>
                    </div>
                </div>

                {{-- Columna derecha: panel del dia seleccionado --}}
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom py-3">
                            <span class="fw-semibold" id="panel-dia-titulo">
                                <i class="bi bi-calendar-check text-success me-1"></i>Selecciona un día
                            </span>
                        </div>
                        <div class="card-body p-3" id="panel-dia-contenido">
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-cursor fs-1 d-block mb-2 opacity-50"></i>
                                Haz clic en un día del calendario para ver sus actividades.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ================================================================
             VISTA SEMANA — nueva: grid de horarios, agendar directo.
             ================================================================ --}}
        <div id="vistaSemana" class="d-none">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" id="btnSemanaAnterior" class="btn btn-outline-secondary btn-sm" title="Semana anterior">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <span class="fw-semibold" id="semanaTitulo">Cargando semana...</span>
                        <button type="button" id="btnSemanaSiguiente" class="btn btn-outline-secondary btn-sm" title="Semana siguiente">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <button type="button" id="btnSemanaHoy" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-calendar-event me-1"></i>Hoy
                    </button>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 align-middle" id="tablaSemana">
                            <thead id="encabezadoSemana"></thead>
                            <tbody id="cuerpoSemana"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ====================================================================
         FORMULARIO (crear/editar) — plantilla oculta, clonada por JS.
         Los <select> ya vienen con las opciones renderizadas por Blade.
         ==================================================================== --}}
    <template id="plantillaFormularioActividad">
        <form id="formActividad" novalidate>
            @csrf
            <input type="hidden" name="fecha" id="campoFecha">
            <input type="hidden" name="hora" id="campoHora">

            <div class="alert alert-light border mb-3 d-flex align-items-center gap-2 py-2 mb-3">
                <i class="bi bi-calendar-event text-success"></i>
                <span id="resumenFechaHora" class="fw-semibold small text-dark"></span>
            </div>

            <div id="alertaErroresForm" class="alert alert-danger d-none"></div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium text-dark">Tipo de Actividad <span class="text-danger">*</span></label>
                    <select name="tipo" id="campoTipo" class="form-select" required>
                        <option value="">-- Seleccione un tipo --</option>
                        <option value="Inhumacion">Inhumación (Entierro)</option>
                        <option value="Exhumacion">Exhumación (Retiro)</option>
                        <option value="Cremacion">Cremación</option>
                        <option value="Anexion">Anexión (Mausoleo)</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-dark">Difunto <span class="text-danger">*</span></label>
                    <select name="difunto_id" id="campoDifunto" class="form-select" required>
                        <option value="">-- Seleccione un difunto --</option>
                        @foreach ($difuntos as $d)
                            <option value="{{ $d->id }}">
                                {{ $d->apellido_paterno }} {{ $d->apellido_materno }} {{ $d->nombre }} ({{ $d->codigo }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6" id="grupoUbicacion" style="display:none;">
                    <label class="form-label fw-medium text-dark">
                        Ubicación <span class="text-danger" id="labelUbicacionReq">*</span>
                        <small class="text-muted" id="labelUbicacionOpc" style="display:none;">(Opcional)</small>
                    </label>
                    <select name="ubicacion_id" id="campoUbicacion" class="form-select">
                        <option value="">-- Seleccione una ubicación --</option>
                        @foreach ($ubicaciones as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->bloque->nombre ?? 'Sin bloque' }} — {{ $u->tipo }} N° {{ $u->numero }} ({{ $u->estado_actual }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-dark">Panteonero <span class="text-muted">(Opcional)</span></label>
                    <select name="panteonero_id" id="campoPanteonero" class="form-select">
                        <option value="">-- Sin asignar --</option>
                        @foreach ($panteoneros as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->apellido_paterno }} {{ $p->apellido_materno }} {{ $p->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium text-dark">Observaciones <span class="text-muted">(Opcional)</span></label>
                    <textarea name="observaciones" id="campoObservaciones" rows="3" class="form-control"
                        placeholder="Detalles adicionales sobre la actividad..."></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success px-4 shadow-sm">
                    <i class="bi bi-check-circle me-1"></i> Guardar
                </button>
            </div>
        </form>
    </template>

    {{-- ====================================================================
         MODAL ÚNICO de actividad — se navega hacia adentro (detalle -> editar
         / cambiar estado), igual patron que ya usamos en Mapa y Semana.
         ==================================================================== --}}
    <div class="modal fade" id="modalActividad" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color:#113615;">
                    <h5 class="modal-title" id="modalActividadTitulo">Actividad</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="modalActividadCuerpo" style="min-height:200px;">
                    <div class="text-center text-muted py-5">
                        <div class="spinner-border spinner-border-sm text-success"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmacion generico (ej. antes de cancelar una actividad) --}}
    <div class="modal fade" id="modalConfirmar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-1 d-block mb-3"></i>
                    <p class="mb-4" id="modalConfirmarTexto">¿Confirmar esta acción?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Volver</button>
                        <button type="button" class="btn btn-danger px-3" id="btnConfirmarAceptar">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenedor de notificaciones toast (Bootstrap nativo) --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;" id="toastContainer"></div>

    @push('styles')
    <style>
        /* Resalta la celda del dia seleccionado en el minicalendario (vista Mes) */
        #mini-calendario .fc-daygrid-day.dia-seleccionado {
            background-color: rgba(17, 54, 21, 0.08);
        }

        /* ---- Grid de la vista Semana ---- */
        #tablaSemana th, #tablaSemana td {
            vertical-align: middle;
        }

        #tablaSemana .columna-horario {
            width: 92px;
            white-space: nowrap;
            font-size: 0.75rem;
            color: #6c757d;
            background-color: #f8f9fa;
        }

        #tablaSemana td.celda-dia {
            padding: 0.35rem;
            min-width: 150px;
        }

        /* Celda vacia: apenas visible hasta el hover, sin parecer un boton obvio */
        .celda-vacia-semana {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 60px;
            border: 1px dashed #e9ecef;
            border-radius: 6px;
            background-color: #fcfcfc;
            color: #ced4da;
            font-size: 0.72rem;
            cursor: pointer;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease;
        }

        .celda-vacia-semana:hover {
            background-color: #eaf5ec;
            border-color: #113615;
            color: #113615;
        }

        /* Celda con actividad: tarjeta compacta clicable */
        .celda-actividad-semana {
            display: block;
            width: 100%;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.4rem 0.5rem;
            background-color: #ffffff;
            text-align: left;
            cursor: pointer;
            font-size: 0.7rem;
        }

        .celda-actividad-semana:hover {
            box-shadow: 0 0 0 2px #113615 inset;
        }
    </style>
    @endpush

    @push('scripts')
    {{-- FullCalendar v6 via CDN (misma libreria que la vista completa) --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calEl              = document.getElementById('mini-calendario');
            var panelTitulo        = document.getElementById('panel-dia-titulo');
            var panelContenido     = document.getElementById('panel-dia-contenido');
            var celdaSeleccionada  = null;

            // Textos e iconos por tipo de actividad (coincide con el badge de la vista Lista)
            var tipoInfo = {
                'Inhumacion': { icono: 'bi-arrow-down-circle', texto: 'Inhumación' },
                'Exhumacion': { icono: 'bi-arrow-up-circle',   texto: 'Exhumación' },
                'Cremacion':  { icono: 'bi-fire',              texto: 'Cremación' },
                'Anexion':    { icono: 'bi-plus-circle',       texto: 'Anexión' },
            };

            var estadoClase = {
                'Pendiente': 'bg-warning text-dark',
                'Realizado': 'bg-success',
                'Cancelado': 'bg-danger',
            };

            var calendar = new FullCalendar.Calendar(calEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                height: 'auto',
                headerToolbar: {
                    left:   'prev,next today',
                    center: 'title',
                    right:  '',
                },
                buttonText: {
                    today: 'Hoy',
                },
                events: '{{ route("agenda.eventos") }}',
                dayMaxEvents: 2,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false,
                    hour12: false,
                },
                // Clic en un dia (no en un evento puntual): actualiza el panel derecho
                dateClick: function (info) {
                    seleccionarDia(info.dateStr, info.dayEl);
                },
                // Clic en un evento puntual: va directo al detalle de la actividad
                eventClick: function (info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.location.href = info.event.url;
                    }
                },
            });
            calendar.render();

            // Al entrar a la pantalla, mostrar el dia de hoy automaticamente
            var hoy = new Date();
            var hoyStr = hoy.getFullYear() + '-' + pad(hoy.getMonth() + 1) + '-' + pad(hoy.getDate());
            var celdaHoy = calEl.querySelector('.fc-day[data-date="' + hoyStr + '"]');
            seleccionarDia(hoyStr, celdaHoy);

            function seleccionarDia(fechaStr, celda) {
                if (celdaSeleccionada) {
                    celdaSeleccionada.classList.remove('dia-seleccionado');
                }
                if (celda) {
                    celda.classList.add('dia-seleccionado');
                    celdaSeleccionada = celda;
                }
                cargarActividadesDelDia(fechaStr);
            }

            function cargarActividadesDelDia(fecha) {
                panelTitulo.innerHTML = '<i class="bi bi-calendar-check text-success me-1"></i>' + formatearFecha(fecha);
                panelContenido.innerHTML = '' +
                    '<div class="text-center text-muted py-5">' +
                        '<div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>' +
                        'Cargando...' +
                    '</div>';

                fetch('{{ route("agenda.porDia") }}?fecha=' + fecha, {
                    headers: { 'Accept': 'application/json' },
                })
                    .then(function (respuesta) {
                        if (!respuesta.ok) throw new Error('Error al consultar el dia.');
                        return respuesta.json();
                    })
                    .then(function (datos) {
                        renderizarActividades(datos.actividades);
                    })
                    .catch(function () {
                        panelContenido.innerHTML = '<div class="alert alert-danger small mb-0">No se pudo cargar la información del día.</div>';
                    });
            }

            function renderizarActividades(actividades) {
                if (!actividades || actividades.length === 0) {
                    panelContenido.innerHTML = '' +
                        '<div class="text-center text-muted py-5">' +
                            '<i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>' +
                            'Sin actividades programadas para este día.' +
                        '</div>';
                    return;
                }

                var html = '';
                actividades.forEach(function (a) {
                    var tipo = tipoInfo[a.tipo] || { icono: 'bi-calendar-event', texto: a.tipo };
                    var clase = estadoClase[a.estado] || 'bg-secondary';

                    html += '' +
                        '<a href="' + a.url + '" class="card border mb-2 text-decoration-none text-reset d-block">' +
                            '<div class="card-body p-3">' +
                                '<div class="d-flex justify-content-between align-items-start mb-2 gap-2">' +
                                    '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">' +
                                        '<i class="bi ' + tipo.icono + ' me-1"></i>' + tipo.texto +
                                    '</span>' +
                                    '<span class="badge ' + clase + '">' + escaparHtml(a.estado) + '</span>' +
                                '</div>' +
                                '<div class="fw-semibold text-dark">' +
                                    '<i class="bi bi-clock text-muted me-1"></i>' + escaparHtml(a.hora) +
                                    ' — ' + escaparHtml(a.difunto) +
                                '</div>' +
                                '<div class="small text-muted mt-1">' +
                                    '<i class="bi bi-geo-alt me-1"></i>' + (a.ubicacion ? escaparHtml(a.ubicacion) : 'Sin ubicación') +
                                '</div>' +
                                '<div class="small text-muted">' +
                                    '<i class="bi bi-person-gear me-1"></i>' + (a.panteonero ? escaparHtml(a.panteonero) : 'Sin panteonero asignado') +
                                '</div>' +
                            '</div>' +
                        '</a>';
                });

                panelContenido.innerHTML = html;
            }

            function formatearFecha(fechaStr) {
                var partes = fechaStr.split('-');
                var fecha = new Date(partes[0], partes[1] - 1, partes[2]);
                var texto = fecha.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' });
                return texto.charAt(0).toUpperCase() + texto.slice(1);
            }

            function pad(numero) {
                return numero < 10 ? '0' + numero : '' + numero;
            }

            function escaparHtml(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }
        });
    </script>

    {{-- ================================================================
         JS de la VISTA SEMANA + MODALES + TOASTS (nuevo).
         ================================================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // =========================================================
            // 0. UTILIDADES COMPARTIDAS
            // =========================================================

            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            var HORARIOS = ['08:00', '09:30', '11:00', '13:00', '14:30', '16:00'];
            var HORARIOS_TEXTO = {
                '08:00': '08:00 – 09:30',
                '09:30': '09:30 – 11:00',
                '11:00': '11:00 – 12:30',
                '13:00': '13:00 – 14:30',
                '14:30': '14:30 – 16:00',
                '16:00': '16:00 – 18:00',
            };

            var tipoInfoSemana = {
                'Inhumacion': { icono: 'bi-arrow-down-circle', texto: 'Inhumación' },
                'Exhumacion': { icono: 'bi-arrow-up-circle',   texto: 'Exhumación' },
                'Cremacion':  { icono: 'bi-fire',              texto: 'Cremación' },
                'Anexion':    { icono: 'bi-plus-circle',       texto: 'Anexión' },
            };

            var estadoClaseSemana = {
                'Pendiente': 'bg-warning text-dark',
                'Realizado': 'bg-success',
                'Cancelado': 'bg-danger',
            };

            function escaparHtml(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }

            function formatearFechaLarga(fechaStr) {
                var partes = fechaStr.split('-');
                var fecha = new Date(partes[0], partes[1] - 1, partes[2]);
                var texto = fecha.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' });
                return texto.charAt(0).toUpperCase() + texto.slice(1);
            }

            function formatearFechaCorta(fechaStr) {
                var partes = fechaStr.split('-');
                var fecha = new Date(partes[0], partes[1] - 1, partes[2]);
                var texto = fecha.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'short' });
                return texto.charAt(0).toUpperCase() + texto.slice(1);
            }

            // ---- Toasts (Bootstrap nativo, sin librerias externas) ----
            function mostrarToast(tipo, mensaje) {
                var contenedor = document.getElementById('toastContainer');
                var clase = tipo === 'success' ? 'text-bg-success' : 'text-bg-danger';
                var icono = tipo === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';

                var toastEl = document.createElement('div');
                toastEl.className = 'toast align-items-center ' + clase + ' border-0';
                toastEl.setAttribute('role', 'alert');
                toastEl.innerHTML = '' +
                    '<div class="d-flex">' +
                        '<div class="toast-body"><i class="bi ' + icono + ' me-2"></i>' + escaparHtml(mensaje) + '</div>' +
                        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
                    '</div>';

                contenedor.appendChild(toastEl);

                var toast = new bootstrap.Toast(toastEl, { delay: 3500 });
                toastEl.addEventListener('hidden.bs.toast', function () { toastEl.remove(); });
                toast.show();
            }

            // =========================================================
            // 1. TOGGLE DE VISTA (Mes / Semana) — JS plano, sin Alpine
            // =========================================================

            var btnVistaMes    = document.getElementById('btnVistaMes');
            var btnVistaSemana = document.getElementById('btnVistaSemana');
            var vistaMes       = document.getElementById('vistaMes');
            var vistaSemana    = document.getElementById('vistaSemana');
            var semanaYaCargada = false;

            btnVistaMes.addEventListener('click', function () {
                vistaMes.classList.remove('d-none');
                vistaSemana.classList.add('d-none');
                btnVistaMes.classList.add('active', 'btn-success');
                btnVistaMes.classList.remove('btn-outline-success');
                btnVistaSemana.classList.remove('active', 'btn-success');
                btnVistaSemana.classList.add('btn-outline-success');
            });

            btnVistaSemana.addEventListener('click', function () {
                vistaSemana.classList.remove('d-none');
                vistaMes.classList.add('d-none');
                btnVistaSemana.classList.add('active', 'btn-success');
                btnVistaSemana.classList.remove('btn-outline-success');
                btnVistaMes.classList.remove('active', 'btn-success');
                btnVistaMes.classList.add('btn-outline-success');

                if (!semanaYaCargada) {
                    semanaYaCargada = true;
                    cargarSemana(fechaBaseSemana);
                }
            });

            // =========================================================
            // 2. GRID DE LA VISTA SEMANA
            // =========================================================

            var hoyIso = new Date();
            var fechaBaseSemana = hoyIso.getFullYear() + '-' + pad2(hoyIso.getMonth() + 1) + '-' + pad2(hoyIso.getDate());
            var semanaActual = null; // ultima respuesta de /agenda/semana, cacheada

            document.getElementById('btnSemanaAnterior').addEventListener('click', function () {
                cambiarSemana(-7);
            });
            document.getElementById('btnSemanaSiguiente').addEventListener('click', function () {
                cambiarSemana(7);
            });
            document.getElementById('btnSemanaHoy').addEventListener('click', function () {
                var h = new Date();
                fechaBaseSemana = h.getFullYear() + '-' + pad2(h.getMonth() + 1) + '-' + pad2(h.getDate());
                cargarSemana(fechaBaseSemana);
            });

            function cambiarSemana(dias) {
                var partes = fechaBaseSemana.split('-').map(Number);
                var fecha = new Date(partes[0], partes[1] - 1, partes[2]);
                fecha.setDate(fecha.getDate() + dias);
                fechaBaseSemana = fecha.getFullYear() + '-' + pad2(fecha.getMonth() + 1) + '-' + pad2(fecha.getDate());
                cargarSemana(fechaBaseSemana);
            }

            function cargarSemana(fecha) {
                document.getElementById('semanaTitulo').textContent = 'Cargando...';

                fetch('{{ route("agenda.semana") }}?fecha=' + fecha, {
                    headers: { 'Accept': 'application/json' },
                })
                    .then(function (r) {
                        if (!r.ok) throw new Error();
                        return r.json();
                    })
                    .then(function (datos) {
                        semanaActual = datos;
                        renderizarGridSemana(datos);
                    })
                    .catch(function () {
                        document.getElementById('semanaTitulo').textContent = 'Error al cargar la semana';
                    });
            }

            function renderizarGridSemana(datos) {
                document.getElementById('semanaTitulo').textContent =
                    formatearFechaCorta(datos.inicio) + ' – ' + formatearFechaCorta(datos.fin);

                // ---- Encabezado: Horario + 7 dias ----
                var htmlEncabezado = '<tr><th class="columna-horario text-center">Horario</th>';
                datos.dias.forEach(function (dia) {
                    htmlEncabezado += '<th class="text-center small">' + escaparHtml(formatearFechaLarga(dia.fecha)) + '</th>';
                });
                htmlEncabezado += '</tr>';
                document.getElementById('encabezadoSemana').innerHTML = htmlEncabezado;

                // ---- Cuerpo: una fila por horario, una celda por dia ----
                var htmlCuerpo = '';
                HORARIOS.forEach(function (horario) {
                    htmlCuerpo += '<tr><td class="columna-horario text-center">' + HORARIOS_TEXTO[horario] + '</td>';

                    datos.dias.forEach(function (dia) {
                        var actividad = dia.actividades ? dia.actividades[horario] : null;
                        htmlCuerpo += '<td class="celda-dia">' + celdaHtml(dia.fecha, horario, actividad) + '</td>';
                    });

                    htmlCuerpo += '</tr>';
                });
                document.getElementById('cuerpoSemana').innerHTML = htmlCuerpo;
            }

            function celdaHtml(fecha, horario, actividad) {
                if (!actividad) {
                    return '' +
                        '<div class="celda-vacia-semana" data-fecha="' + fecha + '" data-horario="' + horario + '">' +
                            '<i class="bi bi-plus-lg me-1"></i>Agregar actividad' +
                        '</div>';
                }

                var tipo  = tipoInfoSemana[actividad.tipo] || { icono: 'bi-calendar-event', texto: actividad.tipo };
                var clase = estadoClaseSemana[actividad.estado] || 'bg-secondary';

                return '' +
                    '<button type="button" class="celda-actividad-semana" data-id="' + actividad.id + '">' +
                        '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle mb-1">' +
                            '<i class="bi ' + tipo.icono + '"></i> ' + tipo.texto +
                        '</span>' +
                        '<span class="badge ' + clase + ' mb-1 float-end">' + escaparHtml(actividad.estado) + '</span>' +
                        '<span class="d-block fw-semibold text-dark text-truncate">' +
                            escaparHtml(actividad.hora) + ' — ' + escaparHtml(actividad.difunto) +
                        '</span>' +
                    '</button>';
            }

            // Delegacion de eventos: un solo listener para todas las celdas del grid
            document.getElementById('cuerpoSemana').addEventListener('click', function (evento) {
                var celdaVacia = evento.target.closest('.celda-vacia-semana');
                if (celdaVacia) {
                    abrirModalCrear(celdaVacia.dataset.fecha, celdaVacia.dataset.horario);
                    return;
                }

                var celdaActividad = evento.target.closest('.celda-actividad-semana');
                if (celdaActividad) {
                    abrirModalDetalle(celdaActividad.dataset.id);
                }
            });

            function pad2(n) {
                return n < 10 ? '0' + n : '' + n;
            }

            // =========================================================
            // 3. MODAL DE ACTIVIDAD (crear / detalle / editar)
            // =========================================================

            var modalActividadEl = document.getElementById('modalActividad');
            var modalActividad   = new bootstrap.Modal(modalActividadEl);
            var modalTitulo       = document.getElementById('modalActividadTitulo');
            var modalCuerpo       = document.getElementById('modalActividadCuerpo');
            var plantillaForm      = document.getElementById('plantillaFormularioActividad');

            // Tipos que requieren ubicacion (igual regla que el formulario anterior)
            var tiposConUbicacion  = ['Inhumacion', 'Exhumacion', 'Anexion', 'Cremacion'];
            var tiposObligatorios  = ['Inhumacion', 'Exhumacion', 'Anexion'];

            /** Inserta el formulario (clonado de la plantilla) dentro del modal y lo prepara. */
            function insertarFormulario() {
                modalCuerpo.innerHTML = '';
                modalCuerpo.appendChild(plantillaForm.content.cloneNode(true));

                var campoTipo = document.getElementById('campoTipo');
                campoTipo.addEventListener('change', actualizarCampoUbicacionSegunTipo);
            }

            function actualizarCampoUbicacionSegunTipo() {
                var tipo = document.getElementById('campoTipo').value;
                var grupo = document.getElementById('grupoUbicacion');
                var campo = document.getElementById('campoUbicacion');
                var labelReq = document.getElementById('labelUbicacionReq');
                var labelOpc = document.getElementById('labelUbicacionOpc');

                if (tiposConUbicacion.includes(tipo)) {
                    grupo.style.display = 'block';
                    var obligatorio = tiposObligatorios.includes(tipo);
                    campo.required = obligatorio;
                    labelReq.style.display = obligatorio ? 'inline' : 'none';
                    labelOpc.style.display = obligatorio ? 'none' : 'inline';
                } else {
                    grupo.style.display = 'none';
                    campo.required = false;
                    campo.value = '';
                }
            }

            // ---- Crear: clic en una celda vacia ----
            function abrirModalCrear(fecha, horario) {
                modalTitulo.textContent = 'Nueva Actividad';
                insertarFormulario();

                document.getElementById('campoFecha').value = fecha;
                document.getElementById('campoHora').value = horario;
                document.getElementById('resumenFechaHora').textContent =
                    formatearFechaLarga(fecha) + ' · ' + HORARIOS_TEXTO[horario];

                document.getElementById('formActividad').dataset.modo = 'crear';
                document.getElementById('formActividad').addEventListener('submit', enviarFormularioActividad);

                modalActividad.show();
            }

            // ---- Detalle: clic en una celda ocupada ----
            function abrirModalDetalle(agendaId) {
                modalTitulo.textContent = 'Detalle de la Actividad';
                modalCuerpo.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm text-success"></div></div>';
                modalActividad.show();

                fetch('{{ url("/agenda") }}/' + agendaId, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
                    .then(renderizarDetalle)
                    .catch(function () {
                        modalCuerpo.innerHTML = '<div class="alert alert-danger mb-0">No se pudo cargar la actividad.</div>';
                    });
            }

            function renderizarDetalle(agenda) {
                modalTitulo.textContent = agenda.codigo + ' — ' + agenda.tipo;

                var tipo  = tipoInfoSemana[agenda.tipo] || { icono: 'bi-calendar-event', texto: agenda.tipo };
                var clase = estadoClaseSemana[agenda.estado] || 'bg-secondary';

                var puedeCambiarEstado = agenda.estado === 'Pendiente' || agenda.estado === 'Realizado';

                modalCuerpo.innerHTML = '' +
                    '<div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">' +
                        '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">' +
                            '<i class="bi ' + tipo.icono + ' me-1"></i>' + tipo.texto +
                        '</span>' +
                        '<span class="badge ' + clase + '">' + escaparHtml(agenda.estado) + '</span>' +
                    '</div>' +
                    '<dl class="row mb-4">' +
                        '<dt class="col-sm-4 text-muted fw-medium">Difunto</dt><dd class="col-sm-8">' + (agenda.difunto ? escaparHtml(agenda.difunto) : '<span class="text-muted">—</span>') + '</dd>' +
                        '<dt class="col-sm-4 text-muted fw-medium">Ubicación</dt><dd class="col-sm-8">' + (agenda.ubicacion ? escaparHtml(agenda.ubicacion) : '<span class="text-muted">Sin ubicación</span>') + '</dd>' +
                        '<dt class="col-sm-4 text-muted fw-medium">Panteonero</dt><dd class="col-sm-8">' + (agenda.panteonero ? escaparHtml(agenda.panteonero) : '<span class="text-muted">Sin asignar</span>') + '</dd>' +
                        '<dt class="col-sm-4 text-muted fw-medium">Fecha</dt><dd class="col-sm-8">' + formatearFechaLarga(agenda.fecha) + '</dd>' +
                        '<dt class="col-sm-4 text-muted fw-medium">Hora</dt><dd class="col-sm-8">' + escaparHtml(agenda.hora) + '</dd>' +
                        '<dt class="col-sm-4 text-muted fw-medium">Observaciones</dt><dd class="col-sm-8">' + (agenda.observaciones ? escaparHtml(agenda.observaciones) : '<span class="text-muted">Sin observaciones</span>') + '</dd>' +
                    '</dl>' +
                    '<div id="zonaCambiarEstado"></div>' +
                    '<div class="d-flex gap-2 border-top pt-3">' +
                        '<button type="button" class="btn btn-outline-secondary" id="btnEditarActividad"><i class="bi bi-pencil me-1"></i>Editar</button>' +
                        (puedeCambiarEstado ? '<button type="button" class="btn btn-outline-success" id="btnMostrarCambiarEstado"><i class="bi bi-arrow-repeat me-1"></i>Cambiar estado</button>' : '') +
                    '</div>';

                document.getElementById('btnEditarActividad').addEventListener('click', function () {
                    abrirModalEditar(agenda);
                });

                if (puedeCambiarEstado) {
                    document.getElementById('btnMostrarCambiarEstado').addEventListener('click', function () {
                        mostrarSelectorEstado(agenda);
                    });
                }
            }

            function mostrarSelectorEstado(agenda) {
                var zona = document.getElementById('zonaCambiarEstado');
                var estados = ['Pendiente', 'Realizado', 'Cancelado'];

                var html = '<div class="alert alert-light border mb-3"><p class="fw-semibold mb-2 small">Nuevo estado:</p><div class="d-flex gap-2 flex-wrap">';
                estados.forEach(function (estado) {
                    var esActual = estado === agenda.estado;
                    html += '<button type="button" class="btn btn-sm ' + (esActual ? 'btn-success' : 'btn-outline-secondary') + ' btn-cambiar-estado" data-estado="' + estado + '"' + (esActual ? ' disabled' : '') + '>' + estado + '</button>';
                });
                html += '</div></div>';
                zona.innerHTML = html;

                zona.querySelectorAll('.btn-cambiar-estado').forEach(function (boton) {
                    boton.addEventListener('click', function () {
                        var nuevoEstado = boton.dataset.estado;

                        if (nuevoEstado === 'Cancelado') {
                            mostrarConfirmacion('¿Confirma cancelar esta actividad?', function () {
                                enviarCambioEstado(agenda.id, nuevoEstado);
                            });
                        } else {
                            enviarCambioEstado(agenda.id, nuevoEstado);
                        }
                    });
                });
            }

            function enviarCambioEstado(agendaId, nuevoEstado) {
                fetch('{{ url("/agenda") }}/' + agendaId + '/estado', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ estado: nuevoEstado }),
                })
                    .then(function (r) { return r.json().then(function (datos) { return { ok: r.ok, datos: datos }; }); })
                    .then(function (resultado) {
                        if (resultado.ok && resultado.datos.success) {
                            mostrarToast('success', resultado.datos.message);
                            modalActividad.hide();
                            if (semanaYaCargada) cargarSemana(fechaBaseSemana);
                        } else {
                            mostrarToast('error', resultado.datos.message || 'No se pudo actualizar el estado.');
                        }
                    })
                    .catch(function () {
                        mostrarToast('error', 'No se pudo actualizar el estado.');
                    });
            }

            // ---- Editar: reutiliza la misma plantilla del formulario ----
            function abrirModalEditar(agenda) {
                modalTitulo.textContent = 'Editar Actividad — ' + agenda.codigo;
                insertarFormulario();

                document.getElementById('campoFecha').value = agenda.fecha;
                document.getElementById('campoHora').value = agenda.hora;
                document.getElementById('resumenFechaHora').textContent =
                    formatearFechaLarga(agenda.fecha) + ' · ' + (HORARIOS_TEXTO[agenda.hora] || agenda.hora) +
                    ' (fecha y hora no se pueden modificar)';

                document.getElementById('campoTipo').value = agenda.tipo;
                document.getElementById('campoDifunto').value = agenda.difunto_id ?? '';
                document.getElementById('campoPanteonero').value = agenda.panteonero_id ?? '';
                document.getElementById('campoObservaciones').value = agenda.observaciones ?? '';
                actualizarCampoUbicacionSegunTipo();
                document.getElementById('campoUbicacion').value = agenda.ubicacion_id ?? '';

                var form = document.getElementById('formActividad');
                form.dataset.modo = 'editar';
                form.dataset.agendaId = agenda.id;
                form.addEventListener('submit', enviarFormularioActividad);
            }

            // ---- Envio del formulario (crear o editar) ----
            function enviarFormularioActividad(evento) {
                evento.preventDefault();

                var form = evento.target;
                var modo = form.dataset.modo;
                var url = modo === 'editar'
                    ? '{{ url("/agenda") }}/' + form.dataset.agendaId
                    : '{{ route("agenda.store") }}';

                // El spoofing de _method (input oculto) solo funciona con
                // formularios multipart/form-data; con fetch + JSON se usa
                // directamente el verbo HTTP real.
                var metodoHttp = modo === 'editar' ? 'PUT' : 'POST';

                var datosFormulario = new FormData(form);
                var cuerpo = {};
                datosFormulario.forEach(function (valor, clave) {
                    if (clave !== '_method' && clave !== '_token') {
                        cuerpo[clave] = valor;
                    }
                });

                var botonGuardar = form.querySelector('button[type="submit"]');
                botonGuardar.disabled = true;

                fetch(url, {
                    method: metodoHttp,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(cuerpo),
                })
                    .then(function (r) { return r.json().then(function (datos) { return { status: r.status, datos: datos }; }); })
                    .then(function (resultado) {
                        botonGuardar.disabled = false;

                        if (resultado.status === 200 && resultado.datos.success) {
                            mostrarToast('success', resultado.datos.message);
                            modalActividad.hide();
                            if (semanaYaCargada) cargarSemana(fechaBaseSemana);
                            return;
                        }

                        // Errores de validacion (422): Laravel devuelve { message, errors: {campo: [..]} }
                        var errores = [];
                        if (resultado.datos.errors) {
                            Object.values(resultado.datos.errors).forEach(function (listaMensajes) {
                                errores = errores.concat(listaMensajes);
                            });
                        }

                        mostrarErroresFormulario(errores.length ? errores : [resultado.datos.message || 'No se pudo guardar la actividad.']);
                        mostrarToast('error', 'Revisa los datos del formulario.');
                    })
                    .catch(function () {
                        botonGuardar.disabled = false;
                        mostrarToast('error', 'No se pudo guardar la actividad.');
                    });
            }

            function mostrarErroresFormulario(mensajes) {
                var alerta = document.getElementById('alertaErroresForm');
                if (!alerta) return;
                alerta.innerHTML = '<ul class="mb-0 ps-3">' + mensajes.map(function (m) { return '<li>' + escaparHtml(m) + '</li>'; }).join('') + '</ul>';
                alerta.classList.remove('d-none');
            }

            // =========================================================
            // 4. MODAL DE CONFIRMACION GENERICO
            // =========================================================

            var modalConfirmarEl = document.getElementById('modalConfirmar');
            var modalConfirmar   = new bootstrap.Modal(modalConfirmarEl);

            function mostrarConfirmacion(texto, alConfirmar) {
                document.getElementById('modalConfirmarTexto').textContent = texto;

                var boton = document.getElementById('btnConfirmarAceptar');
                var botonNuevo = boton.cloneNode(true); // limpia listeners anteriores
                boton.parentNode.replaceChild(botonNuevo, boton);

                botonNuevo.addEventListener('click', function () {
                    modalConfirmar.hide();
                    alConfirmar();
                });

                modalConfirmar.show();
            }

        });
    </script>
    @endpush
</x-app-layout>
