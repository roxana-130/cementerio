<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PanteoneroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // El acceso general ya está protegido por el middleware 'admin'
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtener el ID del panteonero al editar (si existe en la ruta)
        $panteoneroId = $this->route('panteonero') ? $this->route('panteonero')->id : null;

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['required', 'string', 'max:100'],
            'ci' => [
                'required',
                'string',
                'max:20',
                Rule::unique('panteoneros', 'ci')->ignore($panteoneroId),
            ],
        ];
    }

    /**
     * Mensajes de error personalizados en español.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.max' => 'El apellido paterno no debe exceder los 100 caracteres.',
            'apellido_materno.required' => 'El apellido materno es obligatorio.',
            'apellido_materno.max' => 'El apellido materno no debe exceder los 100 caracteres.',
            'ci.required' => 'El CI es obligatorio.',
            'ci.max' => 'El CI no debe exceder los 20 caracteres.',
            'ci.unique' => 'Este número de CI ya se encuentra registrado para otro panteonero.',
        ];
    }
}
