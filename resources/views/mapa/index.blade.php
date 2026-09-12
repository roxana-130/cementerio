@extends('layouts.app')

@section('content')

{{--
    Vista del Mapa Administrativo — Módulo 3 (reconstruido)
    ========================================================
    Interacción en 4 niveles:
      1. Plano general: un rectángulo clicable por bloque (solo los que
         tienen datos reales sembrados), posicionado por porcentaje según
         config/mapa_posiciones.php. Imagen simple posicionada con CSS,
         SIN Leaflet — el mapa geográfico con Leaflet sigue existiendo
         aparte, en la interfaz pública (publico/mapa.blade.php).
      2. Clic en el rectángulo -> panel lateral (offcanvas) con los lados
         disponibles del bloque. El mapa queda siempre visible, sin overlay
         oscuro (offcanvas sin backdrop).
      3. Clic en un lado -> mismo panel, ahora con la cuadrícula de ese
         lado (una celda por ubicación, coloreada por estado).
      4. Clic en una celda -> mismo panel, detalle de la ubicación
         (reutiliza GET /ubicaciones/{id}, con asociar/retirar si es Admin).
--}}

@php
    $esAdmin = Auth::user() && Auth::user()->rol === 'administrador';
@endphp

<style>
    /* .app-main ya es un contenedor flex en columna cuya altura la resuelve
       el grid de AdminLTE; aquí solo evitamos paddings extra alrededor del plano. */
    .app-main {
        padding-bottom: 0;
    }

    /* ---- Nivel 1: plano con rectángulos por bloque ---- */
    #contenedor-plano {
        position: relative;
        max-width: 1100px;
        margin: 0 auto;
    }

    .bloque-rect {
        position: absolute;
        border: 2px solid #113615;
        background-color: rgba(163, 230, 53, 0.35);
        cursor: pointer;
        transition: background-color 0.15s;
    }

    .bloque-rect:hover {
        background-color: rgba(163, 230, 53, 0.65);
    }

    .bloque-rect-etiqueta {
        position: absolute;
        top: -1.5rem;
        left: 0;
        font-size: 0.75rem;
        font-weight: 700;
        color: #113615;
        background: #ffffff;
        padding: 1px 6px;
        border-radius: 4px;
        white-space: nowrap;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    /* ---- Nivel 2: lista de lados ---- */
    .lado-boton {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background-color: #f8f9fa;
        text-align: left;
        transition: background-color 0.15s;
    }

    .lado-boton:hover {
        background-color: #e9f5ea;
        border-color: #113615;
    }

    /* ---- Nivel 3: cuadrícula de ubicaciones ---- */
    .grid-ubicaciones {
        display: grid;
        gap: 6px;
    }

    .celda-ubicacion {
        aspect-ratio: 1;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.68rem;
        font-weight: 700;
        color: #ffffff;
        cursor: pointer;
        transition: transform 0.1s;
    }

    .celda-ubicacion:hover {
        transform: scale(1.12);
        outline: 2px solid #113615;
    }

    /* Los 3 únicos colores de estado para la cuadrícula */
    .celda-disponible   { background-color: #16a34a; } /* verde */
    .celda-ocupado       { background-color: #1e3a5f; } /* azul marino */
    .celda-mantenimiento { background-color: #d97706; } /* naranja */

    /* ---- Badges de estado del panel de detalle (Nivel 4) ---- */
    .badge-disponible    { background-color: #16a34a; color: #fff; }
    .badge-ocupado        { background-color: #1e3a5f; color: #fff; }
    .badge-mantenimiento  { background-color: #d97706; color: #fff; }

    /* ---- Panel lateral (offcanvas) para los Niveles 2-4 ----
       Más ancho que el offcanvas por defecto de Bootstrap (400px) para que
       la cuadrícula de hasta 22 columnas (Bloque A2) entre cómoda. Sin
       backdrop (ver JS): el mapa queda visible y usable en todo momento. */
    #panelMapa {
        --bs-offcanvas-width: 460px;
    }

    @media (max-width: 575.98px) {
        #panelMapa {
            --bs-offcanvas-width: 100vw;
        }
    }
</style>

<div class="container-fluid py-3">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold text-dark mb-0">
                <i class="bi bi-map-fill text-success me-2"></i>Mapa del Cementerio
            </h1>
            <small class="text-muted">Haz clic en un bloque para ver sus lados y ubicaciones.</small>
        </div>
        <div class="d-flex gap-2 flex-wrap small text-muted align-items-center">
            <span><span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:#16a34a;"></span> Disponible</span>
            <span><span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:#1e3a5f;"></span> Ocupado</span>
            <span><span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:#d97706;"></span> Mantenimiento</span>
        </div>
    </div>

    {{-- ====== Nivel 1: plano con rectángulos por bloque ====== --}}
    <div id="contenedor-plano">
        <img src="/images/plano_ cementerio2.png" alt="Plano del cementerio" class="img-fluid w-100 rounded border">
        {{-- Los rectángulos de cada bloque se inyectan por JS a partir de $bloquesJson --}}
    </div>

    @if ($bloquesJson === '[]')
        <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            No hay bloques con datos reales sembrados todavía.
        </div>
    @endif
</div>

{{-- ====== Panel lateral (offcanvas) único para los Niveles 2, 3 y 4
       (se navega "hacia adentro"). Sin backdrop: el mapa queda visible
       y clicable en todo momento, no hay overlay oscuro. ====== --}}
<div class="offcanvas offcanvas-end" id="panelMapa" tabindex="-1" aria-labelledby="panelMapaTitulo">
    <div class="offcanvas-header text-white" style="background-color:#113615;">
        <h5 class="offcanvas-title" id="panelMapaTitulo">Cargando...</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body" id="panelMapaCuerpo">
        <div class="text-center text-muted py-5">
            <div class="spinner-border spinner-border-sm text-success"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================================================
    // 1. RECTÁNGULOS DEL PLANO (Nivel 1)
    // =========================================================

    const bloques = {!! $bloquesJson !!};
    const contenedorPlano = document.getElementById('contenedor-plano');

    bloques.forEach(function (b) {
        const rect = document.createElement('div');
        rect.className = 'bloque-rect';
        rect.style.top = b.top + '%';
        rect.style.left = b.left + '%';
        rect.style.width = b.width + '%';
        rect.style.height = b.height + '%';
        rect.title = b.nombre;

        const etiqueta = document.createElement('span');
        etiqueta.className = 'bloque-rect-etiqueta';
        etiqueta.textContent = b.codigo;
        rect.appendChild(etiqueta);

        rect.addEventListener('click', function () {
            cargarLados(b.codigo, b.nombre);
        });

        contenedorPlano.appendChild(rect);
    });

    // =========================================================
    // 2. ESTADO DE NAVEGACIÓN DEL PANEL LATERAL Y REFERENCIAS
    // =========================================================

    const panelEl   = document.getElementById('panelMapa');
    // Sin backdrop y con scroll habilitado: el mapa de fondo queda visible
    // y usable mientras el panel está abierto (a diferencia de un modal).
    const panel      = new bootstrap.Offcanvas(panelEl, { backdrop: false, scroll: true });
    const panelTitulo = document.getElementById('panelMapaTitulo');
    const panelCuerpo = document.getElementById('panelMapaCuerpo');
    const csrfToken        = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const esAdmin           = {{ $esAdmin ? 'true' : 'false' }};

    // Contexto de navegación, para que los botones "Volver" sepan a dónde regresar.
    let contexto = { bloqueCodigo: null, bloqueNombre: null, lado: null };

    function mostrarCargando() {
        panelCuerpo.innerHTML = `
            <div class="text-center text-muted py-5">
                <div class="spinner-border spinner-border-sm text-success"></div>
            </div>`;
    }

    function mostrarError(mensaje) {
        panelCuerpo.innerHTML = `
            <div class="alert alert-danger mb-0">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>${mensaje}
            </div>`;
    }

    // =========================================================
    // 3. NIVEL 2 — Lados disponibles de un bloque
    // =========================================================

    function cargarLados(bloqueCodigo, bloqueNombre) {
        contexto = { bloqueCodigo: bloqueCodigo, bloqueNombre: bloqueNombre, lado: null };

        panelTitulo.textContent = bloqueNombre;
        mostrarCargando();
        panel.show();

        fetch('{{ url("/mapa/bloque") }}/' + bloqueCodigo, { headers: { 'Accept': 'application/json' } })
            .then(function (r) {
                if (!r.ok) throw new Error();
                return r.json();
            })
            .then(function (data) {
                renderizarLados(data);
            })
            .catch(function () {
                mostrarError('No se pudieron cargar los lados de este bloque.');
            });
    }

    function renderizarLados(data) {
        if (!data.lados || data.lados.length === 0) {
            panelCuerpo.innerHTML = `
                <p class="text-muted mb-0">Este bloque todavía no tiene lados con datos reales.</p>`;
            return;
        }

        let html = '<p class="text-muted small mb-3">Selecciona un lado para ver sus ubicaciones:</p>';

        data.lados.forEach(function (l) {
            const etiquetaTipo = l.tipo === 'Nicho' ? 'nichos' : 'mausoleos';

            html += `
                <button type="button" class="lado-boton" data-lado="${l.lado}">
                    <span class="fw-semibold text-dark">
                        <i class="bi bi-signpost-split text-success me-2"></i>${escaparHtml(l.lado)}
                    </span>
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">
                        ${l.cantidad} ${etiquetaTipo}
                    </span>
                </button>`;
        });

        panelCuerpo.innerHTML = html;

        panelCuerpo.querySelectorAll('.lado-boton').forEach(function (boton) {
            boton.addEventListener('click', function () {
                cargarGrid(boton.dataset.lado);
            });
        });
    }

    // =========================================================
    // 4. NIVEL 3 — Cuadrícula de un bloque + lado
    // =========================================================

    function cargarGrid(lado) {
        contexto.lado = lado;

        panelTitulo.textContent = contexto.bloqueNombre + ' — ' + lado;
        mostrarCargando();

        fetch('{{ url("/mapa/bloque") }}/' + contexto.bloqueCodigo + '/lado/' + lado, {
            headers: { 'Accept': 'application/json' },
        })
            .then(function (r) {
                if (!r.ok) throw new Error();
                return r.json();
            })
            .then(function (data) {
                renderizarGrid(data);
            })
            .catch(function () {
                mostrarError('No se pudo cargar la cuadrícula de este lado.');
            });
    }

    function renderizarGrid(data) {
        panelTitulo.textContent = 'Bloque ' + data.bloque + ' — ' + data.lado + ' — ' + data.tipo;

        let html = `
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="btnVolverALados">
                <i class="bi bi-chevron-left"></i> Volver a lados
            </button>
            <p class="text-muted small mb-3">${data.filas} filas × ${data.columnas} columnas</p>
            <div class="grid-ubicaciones" style="grid-template-columns: repeat(${data.columnas}, 1fr);">`;

        data.ubicaciones.forEach(function (u) {
            const claseEstado = {
                'Disponible':    'celda-disponible',
                'Ocupado':       'celda-ocupado',
                'Mantenimiento': 'celda-mantenimiento',
            }[u.estado] || 'celda-disponible';

            html += `
                <div class="celda-ubicacion ${claseEstado}" data-id="${u.id}" title="N° ${u.numero} — ${escaparHtml(u.estado)}">
                    ${u.numero}
                </div>`;
        });

        html += '</div>';
        panelCuerpo.innerHTML = html;

        document.getElementById('btnVolverALados').addEventListener('click', function () {
            cargarLados(contexto.bloqueCodigo, contexto.bloqueNombre);
        });

        panelCuerpo.querySelectorAll('.celda-ubicacion').forEach(function (celda) {
            celda.addEventListener('click', function () {
                cargarDetalle(celda.dataset.id);
            });
        });
    }

    // =========================================================
    // 5. NIVEL 4 — Detalle de una ubicación (reutiliza /ubicaciones/{id})
    // =========================================================

    function cargarDetalle(ubicacionId) {
        panelTitulo.textContent = 'Detalle de la ubicación';
        mostrarCargando();

        fetch('{{ url("/ubicaciones") }}/' + ubicacionId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        })
            .then(function (r) {
                if (!r.ok) throw new Error();
                return r.json();
            })
            .then(function (data) {
                renderizarDetalle(data);
            })
            .catch(function () {
                mostrarError('No se pudo cargar el detalle de la ubicación.');
            });
    }

    function renderizarDetalle(data) {
        panelTitulo.textContent = data.tipo + ' N° ' + data.numero + ' — Bloque ' + data.bloque + ' ' + data.lado;

        const claseBadge = {
            'Disponible':    'badge-disponible',
            'Ocupado':       'badge-ocupado',
            'Mantenimiento': 'badge-mantenimiento',
        }[data.estado] || 'badge-disponible';

        // Lista de ocupantes activos
        let htmlOcupantes = '';
        if (data.difuntos_activos && data.difuntos_activos.length > 0) {
            data.difuntos_activos.forEach(function (d) {
                let botonRetirar = '';
                if (esAdmin) {
                    botonRetirar = `
                        <form method="POST" action="{{ url('/ubicaciones') }}/${data.id}/retirar" class="d-inline"
                              onsubmit="return confirm('¿Confirmar retiro de ${escaparHtml(d.nombre)}?');">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="difunto_id" value="${d.difunto_id}">
                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Retirar difunto">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>`;
                }
                htmlOcupantes += `
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <div>
                            <span class="d-block fw-semibold small">${escaparHtml(d.nombre)}</span>
                            <span class="text-muted" style="font-size:0.75rem;">
                                ${escaparHtml(d.codigo)} · Ingreso: ${escaparHtml(d.fecha_ingreso ?? '-')}
                            </span>
                        </div>
                        ${botonRetirar}
                    </div>`;
            });
        } else {
            htmlOcupantes = '<p class="text-muted small mb-0">Sin ocupantes activos.</p>';
        }

        // Selector para asociar un difunto (solo Admin y si hay espacio)
        let htmlAsociar = '';
        if (esAdmin) {
            const ocupantesActuales = data.difuntos_activos ? data.difuntos_activos.length : 0;
            const hayEspacio        = ocupantesActuales < data.capacidad;

            if (hayEspacio && data.difuntos_disponibles && data.difuntos_disponibles.length > 0) {
                let opciones = '<option value="">-- Seleccionar difunto --</option>';
                data.difuntos_disponibles.forEach(function (d) {
                    opciones += `<option value="${d.id}">${escaparHtml(d.apellido_paterno)} ${escaparHtml(d.apellido_materno)}, ${escaparHtml(d.nombre)} (${escaparHtml(d.codigo)})</option>`;
                });

                htmlAsociar = `
                    <form method="POST" action="{{ url('/ubicaciones') }}/${data.id}/asociar" class="mt-2">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <select name="difunto_id" class="form-select form-select-sm mb-2" required>
                            ${opciones}
                        </select>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-person-plus me-1"></i>Asociar difunto
                        </button>
                    </form>`;
            } else if (!hayEspacio) {
                htmlAsociar = `<p class="text-danger small mt-2 mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>Ubicación al tope de capacidad (${data.capacidad}).
                </p>`;
            } else {
                htmlAsociar = `<p class="text-muted small mt-2 mb-0">
                    <i class="bi bi-info-circle me-1"></i>No hay difuntos disponibles para asociar.
                </p>`;
            }
        }

        panelCuerpo.innerHTML = `
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="btnVolverAlGrid">
                <i class="bi bi-chevron-left"></i> Volver a ${escaparHtml(contexto.lado)}
            </button>

            <div class="border-bottom pb-3 mb-3 d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small d-block">Bloque ${escaparHtml(data.bloque)} · ${escaparHtml(data.lado)}</span>
                    <h5 class="fw-bold mb-0 mt-1">${escaparHtml(data.tipo)} N° ${data.numero}</h5>
                </div>
                <span class="badge ${claseBadge} rounded-pill px-2 py-1">${escaparHtml(data.estado)}</span>
            </div>

            <div class="row g-2 text-center mb-3">
                <div class="col-4 bg-light rounded p-2">
                    <span class="d-block text-muted small">Col.</span>
                    <span class="fw-bold">${data.columna}</span>
                </div>
                <div class="col-4 bg-light rounded p-2">
                    <span class="d-block text-muted small">Fila</span>
                    <span class="fw-bold">${data.fila}</span>
                </div>
                <div class="col-4 bg-light rounded p-2">
                    <span class="d-block text-muted small">Cap.</span>
                    <span class="fw-bold">${data.capacidad}</span>
                </div>
            </div>

            <div class="mb-3">
                <h6 class="fw-semibold text-dark mb-2">
                    <i class="bi bi-people-fill text-secondary me-1"></i>Ocupantes actuales
                </h6>
                <div class="bg-light p-2 rounded">${htmlOcupantes}</div>
            </div>

            ${esAdmin ? `
            <div class="border-top pt-3">
                <h6 class="fw-semibold text-dark mb-2">
                    <i class="bi bi-person-plus-fill text-success me-1"></i>Asociar difunto
                </h6>
                ${htmlAsociar}
            </div>` : ''}
        `;

        document.getElementById('btnVolverAlGrid').addEventListener('click', function () {
            cargarGrid(contexto.lado);
        });
    }

    // =========================================================
    // 6. UTILIDAD: escapar HTML para evitar XSS al inyectar texto
    // =========================================================

    function escaparHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

}); // fin DOMContentLoaded
</script>
@endpush
