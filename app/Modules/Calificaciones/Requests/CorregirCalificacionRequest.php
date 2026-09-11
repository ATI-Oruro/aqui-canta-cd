<?php

namespace App\Modules\Calificaciones\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorregirCalificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'valores' => ['required', 'array', 'min:1'],
            'valores.*' => ['required', 'numeric', 'min:0'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'valores.required' => 'Debe ingresar las calificaciones de los parámetros.',
            'valores.array' => 'El formato de las calificaciones es inválido.',
            'valores.*.required' => 'Cada parámetro debe tener una calificación.',
            'valores.*.numeric' => 'Cada calificación debe ser un valor numérico.',
            'valores.*.min' => 'La calificación no puede ser menor a 0.',
            'motivo.max' => 'El motivo de corrección no puede superar los 255 caracteres.',
        ];
    }
}
