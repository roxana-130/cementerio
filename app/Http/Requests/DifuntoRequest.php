<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DifuntoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $difuntoId = $this->route('difunto') ? $this->route('difunto')->id : null;

        return [
            'codigo' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('difuntos', 'codigo')->ignore($difuntoId),
            ],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['required', 'string', 'max:100'],
            'apellido_casada' => ['nullable', 'string', 'max:100'],
            'ci' => ['required', 'string', 'max:20'],
            'edad' => ['nullable', 'integer', 'min:0', 'max:120'],
            'profesion_ocupacion' => ['nullable', 'string', 'max:150'],
            'causa_muerte' => ['nullable', 'string'],
            'fecha_fallecimiento' => ['required', 'date', 'before_or_equal:today'],
            'hora_fallecimiento' => ['nullable', 'date_format:H:i'],
            'numero_certificado_defuncion' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Mensajes de error personalizados en español.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder 100 caracteres.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.max' => 'El apellido paterno no debe exceder 100 caracteres.',
            'apellido_materno.required' => 'El apellido materno es obligatorio.',
            'apellido_materno.max' => 'El apellido materno no debe exceder 100 caracteres.',
            'apellido_casada.max' => 'El apellido de casada no debe exceder 100 caracteres.',
            'ci.required' => 'El carnet de identidad (CI) es obligatorio.',
            'ci.max' => 'El CI no debe exceder 20 caracteres.',
            'edad.integer' => 'La edad debe ser un número entero.',
            'edad.min' => 'La edad debe ser mayor o igual a 0.',
            'edad.max' => 'La edad no debe exceder 120 años.',
            'profesion_ocupacion.max' => 'La profesión/ocupación no debe exceder 150 caracteres.',
            'fecha_fallecimiento.required' => 'La fecha de fallecimiento es obligatoria.',
            'fecha_fallecimiento.date' => 'Ingrese una fecha de fallecimiento válida.',
            'fecha_fallecimiento.before_or_equal' => 'La fecha de fallecimiento no puede ser posterior al día de hoy.',
            'hora_fallecimiento.date_format' => 'El formato de hora debe ser HH:MM.',
            'numero_certificado_defuncion.max' => 'El número de certificado de defunción no debe exceder 50 caracteres.',
            'codigo.unique' => 'El código de difunto ya está registrado.',
            'codigo.max' => 'El código no debe exceder 30 caracteres.',
        ];
    }
}
