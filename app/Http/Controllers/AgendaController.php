<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgendaRequest;
use App\Models\Agenda;
use App\Models\Difunto;
use App\Models\Panteonero;
use App\Models\Ubicacion;
use App\Models\UbicacionDifunto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AgendaController extends Controller
{
    /**
     * Franjas horarias fijas del grid de la vista Semana (hora de inicio).
     * Se guarda exactamente este valor en el campo `hora` de la agenda.
     */
    private const HORARIOS = ['08:00', '09:30', '11:00', '13:00', '14:30', '16:00'];

    // =========================================================================
    // METODOS EXISTENTES — NO modificar
    // =========================================================================

    /**
     * Muestra la vista de Agenda (calendario Mes + grid Semana).
     * Las 3 listas (difuntos/panteoneros/ubicaciones) alimentan los
     * selects de los modales de crear/editar actividad del grid Semana.
     */
    public function index()
    {
        $difuntos    = Difunto::where('activo', true)->orderBy('apellido_paterno')->orderBy('nombre')->get();
        $panteoneros = Panteonero::where('activo', true)->orderBy('apellido_paterno')->orderBy('nombre')->get();
        $ubicaciones = Ubicacion::where('activo', true)->with('bloque')->orderBy('id')->get();

        return view('agenda.index', compact('difuntos', 'panteoneros', 'ubicaciones'));
    }

    /** Devuelve JSON para FullCalendar. */
    public function eventos(Request $request)
    {
        $agendas = Agenda::with('difunto')->get();

        $eventos = $agendas->map(function ($agenda) {
            $color = match ($agenda->estado) {
                'Pendiente' => '#f59e0b',
                'Realizado' => '#10b981',
                'Cancelado' => '#ef4444',
                default     => '#6b7280',
            };

            $nombreDifunto = $agenda->difunto
                ? $agenda->difunto->nombre . ' ' . $agenda->difunto->apellido_paterno
                : 'Sin difunto';

            return [
                'id'            => $agenda->id,
                'title'         => '[' . $agenda->tipo . '] ' . $nombreDifunto,
                'start'         => $agenda->fecha->format('Y-m-d') . 'T' . $agenda->hora,
                'url'           => route('agenda.show', $agenda->id),
                'color'         => $color,
                'extendedProps' => [
                    'estado' => $agenda->estado,
                    'codigo' => $agenda->codigo,
                ],
            ];
        });

        return response()->json($eventos);
    }

    // =========================================================================
    // METODOS NUEVOS — Vistas de presentacion del calendario (solo lectura)
    // No modifican datos ni tocan el automatismo.
    // =========================================================================

    /** Muestra el calendario FullCalendar a pantalla completa (mes/semana/lista). */
    public function completo()
    {
        return view('agenda.completo');
    }

    /**
     * Lista en tabla de las actividades de agenda, con busqueda y filtros.
     * Mismo patron de busqueda/paginacion que Difuntos y Panteoneros.
     */
    public function lista(Request $request)
    {
        $query = Agenda::with('difunto');

        if ($request->filled('buscar')) {
            $buscar = trim($request->input('buscar'));
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'LIKE', "%{$buscar}%")
                  ->orWhereHas('difunto', function ($q2) use ($buscar) {
                      $q2->where('nombre', 'LIKE', "%{$buscar}%")
                          ->orWhere('apellido_paterno', 'LIKE', "%{$buscar}%")
                          ->orWhere('apellido_materno', 'LIKE', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $agendas = $query->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('agenda.lista', compact('agendas'));
    }

    /**
     * Devuelve en JSON las actividades de una fecha especifica.
     * Usado por el panel lateral del calendario compacto (fetch de resources/views/agenda/index.blade.php).
     */
    public function porDia(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
        ]);

        $agendas = Agenda::with('difunto', 'ubicacion.bloque', 'panteonero')
            ->whereDate('fecha', $request->input('fecha'))
            ->orderBy('hora')
            ->get();

        $actividades = $agendas->map(function (Agenda $agenda) {
            return [
                'id'         => $agenda->id,
                'codigo'     => $agenda->codigo,
                'hora'       => substr($agenda->hora, 0, 5),
                'tipo'       => $agenda->tipo,
                'estado'     => $agenda->estado,
                'difunto'    => $agenda->difunto
                    ? "{$agenda->difunto->nombre} {$agenda->difunto->apellido_paterno} {$agenda->difunto->apellido_materno}"
                    : 'Sin difunto',
                'ubicacion'  => $agenda->ubicacion
                    ? ($agenda->ubicacion->bloque->nombre ?? '') . ' — ' . $agenda->ubicacion->tipo . ' N° ' . $agenda->ubicacion->numero
                    : null,
                'panteonero' => $agenda->panteonero
                    ? "{$agenda->panteonero->nombre} {$agenda->panteonero->apellido_paterno}"
                    : null,
                'url'        => route('agenda.show', $agenda->id),
            ];
        });

        return response()->json([
            'fecha'       => $request->input('fecha'),
            'actividades' => $actividades,
        ]);
    }

    // =========================================================================
    // CRUD — Modulo 5
    // =========================================================================

    /**
     * Guarda una nueva actividad.
     * Estado inicial = Pendiente. Genera codigo AGE-XXXX.
     *
     * Se llama por fetch() desde el grid de la vista Semana (JSON), ya que
     * la pagina de creacion independiente ya no existe.
     */
    public function store(AgendaRequest $request)
    {
        $datos = $request->validated();

        // Generar codigo correlativo AGE-XXXX (igual patron que DIF-XXXX)
        $maxId = Agenda::max('id') ?? 0;
        $datos['codigo']     = 'AGE-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
        $datos['estado']     = 'Pendiente';
        $datos['usuario_id'] = auth()->id();

        $agenda = Agenda::create($datos);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Actividad ' . $agenda->codigo . ' registrada correctamente.',
            ]);
        }

        return redirect()
            ->route('agenda.show', $agenda)
            ->with('success', 'Actividad ' . $agenda->codigo . ' registrada correctamente.');
    }

    /**
     * Detalle de una actividad de agenda.
     * Si se pide JSON (fetch del modal del grid Semana), devuelve los datos
     * en vez de la pagina completa; el comportamiento de pagina completa
     * (usado por el clic en un evento del calendario Mes) no cambia.
     */
    public function show(Request $request, Agenda $agenda)
    {
        $agenda->load('difunto', 'ubicacion.bloque', 'panteonero', 'usuario');

        if ($request->wantsJson()) {
            return response()->json([
                'id'            => $agenda->id,
                'codigo'        => $agenda->codigo,
                'tipo'          => $agenda->tipo,
                'estado'        => $agenda->estado,
                'fecha'         => $agenda->fecha->format('Y-m-d'),
                'hora'          => substr($agenda->hora, 0, 5),
                'observaciones' => $agenda->observaciones,
                'difunto_id'    => $agenda->difunto_id,
                'ubicacion_id'  => $agenda->ubicacion_id,
                'panteonero_id' => $agenda->panteonero_id,
                'difunto'       => $agenda->difunto
                    ? "{$agenda->difunto->nombre} {$agenda->difunto->apellido_paterno} {$agenda->difunto->apellido_materno}"
                    : null,
                'ubicacion'     => $agenda->ubicacion
                    ? ($agenda->ubicacion->bloque->nombre ?? '') . ' — ' . $agenda->ubicacion->tipo . ' N° ' . $agenda->ubicacion->numero
                    : null,
                'panteonero'    => $agenda->panteonero
                    ? "{$agenda->panteonero->nombre} {$agenda->panteonero->apellido_paterno}"
                    : null,
                'usuario'       => $agenda->usuario->name ?? null,
            ]);
        }

        return view('agenda.show', compact('agenda'));
    }

    /** Formulario de edicion. Campos bloqueados si estado = Realizado. */
    public function edit(Agenda $agenda)
    {
        $difuntos    = Difunto::where('activo', true)->orderBy('apellido_paterno')->orderBy('nombre')->get();
        $panteoneros = Panteonero::where('activo', true)->orderBy('apellido_paterno')->orderBy('nombre')->get();
        $ubicaciones = Ubicacion::where('activo', true)->with('bloque')->orderBy('id')->get();

        $agenda->load('difunto', 'ubicacion.bloque', 'panteonero');

        return view('agenda.edit', compact('agenda', 'difuntos', 'panteoneros', 'ubicaciones'));
    }

    /**
     * Actualiza la actividad.
     * BLOQUEO: si estado = Realizado, difunto_id, ubicacion_id, tipo y fecha
     * no se actualizan aunque vengan en el request.
     */
    public function update(AgendaRequest $request, Agenda $agenda)
    {
        $datos = $request->validated();
        $mensajeBloqueo = '';

        if ($agenda->estado === 'Realizado') {
            $intentoCambiar = (
                (isset($datos['difunto_id'])   && $datos['difunto_id']   != $agenda->difunto_id)   ||
                (isset($datos['ubicacion_id']) && $datos['ubicacion_id'] != $agenda->ubicacion_id) ||
                (isset($datos['tipo'])         && $datos['tipo']         !== $agenda->tipo)         ||
                (isset($datos['fecha'])        && $datos['fecha']        !== $agenda->fecha->format('Y-m-d'))
            );

            unset($datos['difunto_id'], $datos['ubicacion_id'], $datos['tipo'], $datos['fecha']);

            if ($intentoCambiar) {
                $mensajeBloqueo = 'Nota: los campos Difunto, Ubicacion, Tipo y Fecha no se pueden modificar porque la actividad ya fue marcada como Realizado.';
            }
        }

        $agenda->update($datos);

        $mensaje = 'Actividad actualizada correctamente.';
        if ($mensajeBloqueo) {
            $mensaje .= ' ' . $mensajeBloqueo;
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $mensaje]);
        }

        return redirect()->route('agenda.show', $agenda)->with('success', $mensaje);
    }

    /**
     * Cambia el estado de una actividad.
     * Si pasa a Realizado por primera vez, ejecuta el automatismo.
     */
    public function cambiarEstado(Request $request, Agenda $agenda)
    {
        $request->validate([
            'estado' => 'required|in:Pendiente,Realizado,Cancelado',
        ]);

        $estadoAnterior = $agenda->estado;
        $nuevoEstado    = $request->input('estado');

        $agenda->update(['estado' => $nuevoEstado]);

        // Ejecutar automatismo SOLO si pasa a Realizado por primera vez
        if ($nuevoEstado === 'Realizado' && $estadoAnterior !== 'Realizado') {
            $this->ejecutarAutomatismoRealizado($agenda);
        }

        // Si "Cancelado": sin automatismo, sin revision.
        // Si vuelve de "Realizado": se permite el cambio pero NO se revierte
        // ubicacion_difunto. NOTA: si se requiere revertir, hacerlo desde Mapa.

        $mensaje = $nuevoEstado === 'Realizado'
            ? 'Se registró correctamente la actividad en el sistema.'
            : 'Estado de ' . $agenda->codigo . ' actualizado a "' . $nuevoEstado . '".';

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $mensaje]);
        }

        return redirect()->route('agenda.show', $agenda)->with('success', $mensaje);
    }

    /**
     * Devuelve en JSON las actividades de la semana (lunes a domingo) que
     * contiene la fecha indicada (o la de hoy si no se envia `fecha`).
     * Usado por el grid de la vista Semana.
     */
    public function semana(Request $request)
    {
        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->input('fecha'))
            : Carbon::today();

        $inicioSemana = $fecha->copy()->startOfWeek(Carbon::MONDAY);
        $finSemana    = $fecha->copy()->endOfWeek(Carbon::SUNDAY);

        $agendas = Agenda::with('difunto', 'ubicacion.bloque', 'panteonero')
            ->whereBetween('fecha', [$inicioSemana->toDateString(), $finSemana->toDateString()])
            ->get();

        $dias = [];
        for ($i = 0; $i < 7; $i++) {
            $fechaDia = $inicioSemana->copy()->addDays($i);

            $actividadesDelDia = $agendas
                ->filter(fn (Agenda $a) => $a->fecha->isSameDay($fechaDia))
                ->mapWithKeys(fn (Agenda $a) => [substr($a->hora, 0, 5) => $this->serializarAgendaCorta($a)]);

            // La etiqueta en español ("Lunes 10 de septiembre") se arma en el
            // JS con toLocaleDateString('es-ES', ...), igual que en la vista Mes
            // (el locale de la app es 'en', asi que translatedFormat() no sirve aqui).
            $dias[] = [
                'fecha'       => $fechaDia->toDateString(),
                'actividades' => $actividadesDelDia,
            ];
        }

        return response()->json([
            'inicio'   => $inicioSemana->toDateString(),
            'fin'      => $finSemana->toDateString(),
            'horarios' => self::HORARIOS,
            'dias'     => $dias,
        ]);
    }

    /**
     * Representacion corta de una actividad para la tarjeta del grid Semana.
     */
    private function serializarAgendaCorta(Agenda $agenda): array
    {
        return [
            'id'      => $agenda->id,
            'codigo'  => $agenda->codigo,
            'tipo'    => $agenda->tipo,
            'estado'  => $agenda->estado,
            'hora'    => substr($agenda->hora, 0, 5),
            'difunto' => $agenda->difunto
                ? "{$agenda->difunto->nombre} {$agenda->difunto->apellido_paterno}"
                : 'Sin difunto',
        ];
    }

    // =========================================================================
    // METODO PRIVADO — Automatismo al marcar como Realizado
    // =========================================================================

    /**
     * Ejecuta el automatismo en ubicacion_difunto segun el tipo de actividad.
     * Solo se llama cuando el estado pasa a "Realizado" por primera vez.
     * Cada case del switch tiene comentarios para la defensa del proyecto.
     */
    private function ejecutarAutomatismoRealizado(Agenda $agenda): void
    {
        switch ($agenda->tipo) {

            case 'Inhumacion':
                // Entierro: se crea un registro activo en ubicacion_difunto.
                // El difunto ocupa esa ubicacion a partir de la fecha de la agenda.
                UbicacionDifunto::create([
                    'ubicacion_id'  => $agenda->ubicacion_id,
                    'difunto_id'    => $agenda->difunto_id,
                    'fecha_ingreso' => $agenda->fecha,
                    'activo'        => true,
                ]);
                break;

            case 'Anexion':
                // Igual que Inhumacion: crea un registro activo nuevo.
                // La ubicacion puede ya tener otros activos (ej. mausoleo familiar).
                UbicacionDifunto::create([
                    'ubicacion_id'  => $agenda->ubicacion_id,
                    'difunto_id'    => $agenda->difunto_id,
                    'fecha_ingreso' => $agenda->fecha,
                    'activo'        => true,
                ]);
                break;

            case 'Exhumacion':
                // Retira al difunto: busca el registro activo y lo cierra.
                // activo = false, fecha_salida = fecha de la agenda.
                // Esto hace que Ubicacion muestre "Mantenimiento" por 14 dias.
                $registroActivo = UbicacionDifunto::where('ubicacion_id', $agenda->ubicacion_id)
                    ->where('difunto_id', $agenda->difunto_id)
                    ->where('activo', true)
                    ->first();

                if ($registroActivo) {
                    $registroActivo->update([
                        'fecha_salida' => $agenda->fecha,
                        'activo'       => false,
                    ]);
                } else {
                    Log::warning('Exhumacion: no se encontro registro activo en ubicacion_difunto.', [
                        'agenda_id'    => $agenda->id,
                        'difunto_id'   => $agenda->difunto_id,
                        'ubicacion_id' => $agenda->ubicacion_id,
                    ]);
                }
                break;

            case 'Cremacion':
                // Si ubicacion_id != null (familia deposita urna): igual que Inhumacion.
                // Si ubicacion_id = null (familia lleva restos): no se hace nada.
                if ($agenda->ubicacion_id !== null) {
                    UbicacionDifunto::create([
                        'ubicacion_id'  => $agenda->ubicacion_id,
                        'difunto_id'    => $agenda->difunto_id,
                        'fecha_ingreso' => $agenda->fecha,
                        'activo'        => true,
                    ]);
                }
                break;
        }
    }
}