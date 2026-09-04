<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // El acceso general ya está restringido por el middleware 'admin'
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtener el ID del usuario al editar (si existe en la ruta)
        $usuarioId = $this->route('usuario') ? $this->route('usuario')->id : null;

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($usuarioId),
            ],
            'rol' => ['required', 'string', Rule::in(['administrador', 'personal'])],
        ];

        // Contraseña obligatoria al crear, opcional al editar (reseteo manual)
        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'string', 'min:8'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8'];
        }

        return $rules;
    }

    /**
     * Mensajes de error personalizados en español.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre completo es obligatorio.',
            'name.max' => 'El nombre no debe exceder los 150 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria para nuevos usuarios.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'rol.required' => 'Debe seleccionar un rol para el usuario.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ];
    }
}
