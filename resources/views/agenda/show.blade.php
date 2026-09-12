<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h4 fw-bold text-dark mb-0">
                    <i class="bi bi-calendar-event me-2 text-success"></i>
                    {{ $agenda->codigo }} — {{ $agenda->tipo }}
                </h1>
                <small class="text-muted">Detalle de la actividad de agenda</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('agenda.edit', $agenda) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="{{ route('agenda.index') }}" class="btn btn-light border btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container-fluid py-4">

        {{-- Mensajes de exito --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Enviar ubicacion por correo — funcionalidad separada de la de arriba,
             no toca la logica de cambio de estado ni de edicion. --}}
        @php
            // El tipo de la agenda no lleva tilde (Inhumacion, Exhumacion, ...);
            // el select del correo usa las etiquetas oficiales con tilde.
            $tiposServicioConTilde = [
                'Inhumacion' => 'Inhumación',
                'Exhumacion' => 'Exhumación',
                'Cremacion'  => 'Cremación',
                'Anexion'    => 'Anexión',
            ];
            $tipoServicioSugerido = $tiposServicioConTilde[$agenda->tipo] ?? 'Inhumación';
        @endphp
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span class="text-muted small">
                    <i class="bi bi-envelope text-success me-1"></i>
                    Enviar por correo la ficha de servicio funerario a un familiar.
                </span>
                <button type="button" class="btn btn-primary" id="btn-enviar-correo"
                    data-nombre-difunto="{{ $agenda->difunto ? trim($agenda->difunto->nombre . ' ' . $agenda->difunto->apellido_paterno . ' ' . $agenda->difunto->apellido_materno) : 'Sin difunto' }}"
                    data-ubicacion-fisica="{{ $agenda->ubicacion ? trim(($agenda->ubicacion->bloque->nombre ?? '') . ' — ' . $agenda->ubicacion->lado . ' — ' . $agenda->ubicacion->tipo . ' N° ' . $agenda->ubicacion->numero) : 'Sin ubicación asignada' }}"
                    data-fecha-servicio="{{ $agenda->fecha->format('Y-m-d') }}"
                    data-hora-servicio="{{ substr($agenda->hora, 0, 5) }}"
                    data-tipo-servicio="{{ $tipoServicioSugerido }}"
                    onclick="abrirModalCorreo()">
                    📧 Enviar Ubicación por Correo
                </button>
            </div>
        </div>

        <div class="row g-4">

            {{-- Panel izquierdo: datos de la actividad --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle text-success fs-5"></i>
                        <span class="fw-semibold">Datos de la Actividad</span>

                        {{-- Badge de estado --}}
                        @if ($agenda->estado === 'Realizado')
                            <span class="badge bg-success ms-auto fs-6">Realizado</span>
                        @elseif ($agenda->estado === 'Cancelado')
                            <span class="badge bg-danger ms-auto fs-6">Cancelado</span>
                        @else
                            <span class="badge bg-warning text-dark ms-auto fs-6">Pendiente</span>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <dl class="row mb-0">

                            <dt class="col-sm-4 text-muted fw-medium">Codigo</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $agenda->codigo }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Tipo</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle fs-6 px-3">
                                    {{ $agenda->tipo }}
                                </span>
                            </dd>

                            <dt class="col-sm-4 text-muted fw-medium">Difunto</dt>
                            <dd class="col-sm-8">
                                @if ($agenda->difunto)
                                    <a href="{{ route('difuntos.show', $agenda->difunto) }}" class="text-decoration-none fw-semibold">
                                        {{ $agenda->difunto->apellido_paterno }}
                                        {{ $agenda->difunto->apellido_materno }}
                                        {{ $agenda->difunto->nombre }}
                                    </a>
                                    <small class="text-muted">({{ $agenda->difunto->codigo }})</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4 text-muted fw-medium">Ubicacion</dt>
                            <dd class="col-sm-8">
                                @if ($agenda->ubicacion)
                                    {{ $agenda->ubicacion->bloque->nombre ?? '' }} —
                                    {{ $agenda->ubicacion->tipo }} N° {{ $agenda->ubicacion->numero }}
                                    <span class="badge bg-secondary ms-1">{{ $agenda->ubicacion->estado_actual }}</span>
                                @else
                                    <span class="text-muted">Sin ubicacion asignada</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4 text-muted fw-medium">Panteonero</dt>
                            <dd class="col-sm-8">
                                @if ($agenda->panteonero)
                                    {{ $agenda->panteonero->apellido_paterno }}
                                    {{ $agenda->panteonero->apellido_materno }}
                                    {{ $agenda->panteonero->nombre }}
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4 text-muted fw-medium">Fecha</dt>
                            <dd class="col-sm-8">{{ $agenda->fecha->format('d/m/Y') }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Hora</dt>
                            <dd class="col-sm-8">{{ substr($agenda->hora, 0, 5) }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Registrado por</dt>
                            <dd class="col-sm-8">{{ $agenda->usuario->name ?? '—' }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Observaciones</dt>
                            <dd class="col-sm-8">
                                @if ($agenda->observaciones)
                                    <span class="text-dark">{{ $agenda->observaciones }}</span>
                                @else
                                    <span class="text-muted">Sin observaciones</span>
                                @endif
                            </dd>

                        </dl>
                    </div>
                </div>
            </div>

            {{-- Panel derecho: acciones de estado --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <span class="fw-semibold"><i class="bi bi-arrow-repeat me-2 text-success"></i>Cambiar Estado</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column gap-3">

                        @if ($agenda->estado === 'Pendiente')

                            {{-- Marcar como Realizado --}}
                            <form method="POST" action="{{ route('agenda.cambiarEstado', $agenda) }}"
                                onsubmit="return confirm('¿Confirma marcar esta actividad como REALIZADO? Esto ejecutara el automatismo en la base de datos (ubicacion_difunto).')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="Realizado">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check2-circle me-1"></i> Marcar como Realizado
                                </button>
                            </form>

                            {{-- Cancelar actividad --}}
                            <form method="POST" action="{{ route('agenda.cambiarEstado', $agenda) }}"
                                onsubmit="return confirm('¿Confirma CANCELAR esta actividad? Esta accion no se revertira automaticamente.')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="Cancelado">
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-x-circle me-1"></i> Cancelar Actividad
                                </button>
                            </form>

                        @elseif ($agenda->estado === 'Realizado')

                            <div class="alert alert-success py-2 mb-0">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                Actividad realizada. El automatismo ya fue ejecutado.
                            </div>

                            {{-- Cancelar (permitido pero sin reversion automatica) --}}
                            <form method="POST" action="{{ route('agenda.cambiarEstado', $agenda) }}"
                                onsubmit="return confirm('ATENCION: Cancelar una actividad ya realizada NO revierte automaticamente el registro en ubicacion_difunto. Si necesita revertirlo, hagalo desde el modulo Mapa. ¿Continuar?')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="Cancelado">
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-x-circle me-1"></i> Cancelar Actividad
                                </button>
                            </form>

                        @elseif ($agenda->estado === 'Cancelado')

                            <div class="alert alert-danger py-2 mb-0">
                                <i class="bi bi-x-circle-fill me-1"></i> Actividad cancelada.
                            </div>

                            {{-- Volver a Pendiente --}}
                            <form method="POST" action="{{ route('agenda.cambiarEstado', $agenda) }}"
                                onsubmit="return confirm('¿Confirma volver esta actividad a estado PENDIENTE?')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="Pendiente">
                                <button type="submit" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Volver a Pendiente
                                </button>
                            </form>

                        @endif

                        <hr class="my-1">
                        <a href="{{ route('agenda.edit', $agenda) }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-pencil me-1"></i> Editar Actividad
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ====================================================================
         MODAL: enviar ubicacion por correo (ficha de servicio funerario)
         ==================================================================== --}}

    <div class="modal fade" id="modalCorreo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color:#113615;">
                    <h5 class="modal-title">Enviar ubicación por correo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="correoDestinoInput" class="form-label fw-medium text-dark">Correo de destino</label>
                        <input type="email" id="correoDestinoInput" class="form-control" placeholder="correo@ejemplo.com" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="fechaServicioInput" class="form-label fw-medium text-dark">Fecha del Servicio</label>
                            <input type="date" id="fechaServicioInput" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="horaServicioInput" class="form-label fw-medium text-dark">Hora del Servicio</label>
                            <input type="time" id="horaServicioInput" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="tipoServicioInput" class="form-label fw-medium text-dark">Tipo de Servicio</label>
                            <select id="tipoServicioInput" class="form-select" required>
                                <option value="Inhumación">Inhumación</option>
                                <option value="Exhumación">Exhumación</option>
                                <option value="Cremación">Cremación</option>
                                <option value="Anexión">Anexión</option>
                            </select>
                        </div>
                    </div>

                </div>
                //-------------------------
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btnEnviarCorreo" onclick="enviarCorreo()">
                        <i class="bi bi-send me-1"></i>Enviar correo
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toasts (Bootstrap nativo, mismo patron que en Agenda / Mapa) --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;" id="toastContainerCorreo"></div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            var modalCorreoEl = document.getElementById('modalCorreo');
            var modalCorreo   = new bootstrap.Modal(modalCorreoEl);

            window.abrirModalCorreo = function () {
                var botonPrincipal = document.getElementById('btn-enviar-correo');

                document.getElementById('correoDestinoInput').value = '';
                // Fecha/hora/tipo se pre-llenan con los datos de la actividad actual,
                // pero quedan editables (el usuario puede cambiarlos si corresponde).
                document.getElementById('fechaServicioInput').value = botonPrincipal.dataset.fechaServicio || '';
                document.getElementById('horaServicioInput').value = botonPrincipal.dataset.horaServicio || '';
                document.getElementById('tipoServicioInput').value = botonPrincipal.dataset.tipoServicio || 'Inhumación';
                modalCorreo.show();
            };

            window.enviarCorreo = function () {
                var correoInput = document.getElementById('correoDestinoInput');
                var fechaInput  = document.getElementById('fechaServicioInput');
                var horaInput   = document.getElementById('horaServicioInput');
                var tipoInput   = document.getElementById('tipoServicioInput');

                if (!correoInput.value.trim() || !correoInput.checkValidity()) {
                    mostrarToastCorreo('error', 'Ingresa un correo de destino válido.');
                    return;
                }
                if (!fechaInput.value || !horaInput.value || !tipoInput.value) {
                    mostrarToastCorreo('error', 'Completa la fecha, la hora y el tipo de servicio.');
                    return;
                }

                var boton = document.getElementById('btnEnviarCorreo');
                boton.disabled = true;

                var botonPrincipal   = document.getElementById('btn-enviar-correo');
                var nombreDifunto    = botonPrincipal.dataset.nombreDifunto;
                var ubicacionFisica  = botonPrincipal.dataset.ubicacionFisica;

                fetch('{{ route("correos.enviarUbicacion") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        nombreDifunto: nombreDifunto,
                        ubicacionFisica: ubicacionFisica,
                        correoDestino: correoInput.value.trim(),
                        fechaServicio: fechaInput.value,
                        horaServicio: horaInput.value,
                        tipoServicio: tipoInput.value,
                    }),
                })
                    .then(function (r) { return r.json().then(function (datos) { return { ok: r.ok, datos: datos }; }); })
                    .then(function (resultado) {
                        boton.disabled = false;

                        if (resultado.ok && resultado.datos.success) {
                            mostrarToastCorreo('success', resultado.datos.message || 'Correo enviado exitosamente.');
                            modalCorreo.hide();
                            return;
                        }

                        var mensaje = resultado.datos.error;
                        if (!mensaje && resultado.datos.errors) {
                            mensaje = Object.values(resultado.datos.errors).flat().join(' ');
                        }
                        mostrarToastCorreo('error', mensaje || 'No se pudo enviar el correo.');
                    })
                    .catch(function () {
                        boton.disabled = false;
                        mostrarToastCorreo('error', 'No se pudo enviar el correo. Intenta nuevamente.');
                    });
            };

            function mostrarToastCorreo(tipo, mensaje) {
                var contenedor = document.getElementById('toastContainerCorreo');
                var clase = tipo === 'success' ? 'text-bg-success' : 'text-bg-danger';
                var icono = tipo === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';

                var toastEl = document.createElement('div');
                toastEl.className = 'toast align-items-center ' + clase + ' border-0';
                toastEl.setAttribute('role', 'alert');
                toastEl.innerHTML = '' +
                    '<div class="d-flex">' +
                        '<div class="toast-body"><i class="bi ' + icono + ' me-2"></i>' + escaparHtmlCorreo(mensaje) + '</div>' +
                        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
                    '</div>';

                contenedor.appendChild(toastEl);

                var toast = new bootstrap.Toast(toastEl, { delay: 3500 });
                toastEl.addEventListener('hidden.bs.toast', function () { toastEl.remove(); });
                toast.show();
            }

            function escaparHtmlCorreo(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }

        });
    </script>
    @endpush
</x-app-layout>