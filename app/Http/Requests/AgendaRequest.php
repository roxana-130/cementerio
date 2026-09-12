<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'tipo'          => 'required|in:Inhumacion,Exhumacion,Cremacion,Anexion',
            'difunto_id'    => 'required|exists:difuntos,id',
            'ubicacion_id'  => 'required_if:tipo,Inhumacion,Exhumacion,Anexion|nullable|exists:ubicaciones,id',
            'panteonero_id' => 'nullable|exists:panteoneros,id',
            'fecha'         => 'required|date',
            // Dos actividades no pueden compartir fecha+hora (mismo horario del
            // grid de la vista Semana), salvo que la otra este Cancelada.
            // Rule::unique ignora el propio registro al editar.
            'hora'          => [
                'required',
                Rule::unique('agendas')
                    ->where(fn ($query) => $query
                        ->where('fecha', $this->input('fecha'))
                        ->where('estado', '!=', 'Cancelado'))
                    ->ignore($this->route('agenda')),
            ],
            'observaciones' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required'            => 'Debe seleccionar el tipo de actividad.',
            'tipo.in'                  => 'El tipo de actividad no es valido.',
            'difunto_id.required'      => 'Debe seleccionar un difunto.',
            'difunto_id.exists'        => 'El difunto seleccionado no existe.',
            'ubicacion_id.required_if' => 'La ubicacion es obligatoria para el tipo de actividad seleccionado.',
            'ubicacion_id.exists'      => 'La ubicacion seleccionada no existe.',
            'panteonero_id.exists'     => 'El panteonero seleccionado no existe.',
            'fecha.required'           => 'La fecha es obligatoria.',
            'fecha.date'               => 'La fecha no tiene un formato valido.',
            'hora.required'            => 'La hora es obligatoria.',
            'hora.unique'              => 'Ya existe una actividad programada en ese día y horario.',
            'observaciones.max'        => 'Las observaciones no pueden superar los 1000 caracteres.',
        ];
    }
}