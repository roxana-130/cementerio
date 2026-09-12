<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistorialController extends Controller
{
    /**
     * Muestra la lista de auditoría de cambios del sistema (solo lectura).
     */
    public function index(Request $request): View
    {
        $query = Historial::with('user');

        // Filtro por tabla afectada
        if ($request->filled('tabla')) {
            $query->where('tabla', $request->input('tabla'));
        }

        // Filtro por usuario que realizó la acción
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->input('fecha_desde'));
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->input('fecha_hasta'));
        }

        $historiales = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $usuarios = User::orderBy('name', 'asc')->get(['id', 'name']);

        return view('historial.index', compact('historiales', 'usuarios'));
    }
}
