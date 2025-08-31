<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssuntoRequest extends FormRequest
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
        $assuntoId = $this->route('assunto');
        
        return [
            'Descricao' => 'sometimes|required|string|max:20|unique:assuntos,Descricao,' . $assuntoId . ',codAs',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'Descricao.required' => 'A descrição do assunto é obrigatória.',
            'Descricao.max' => 'A descrição do assunto não pode ter mais de 20 caracteres.',
            'Descricao.unique' => 'Já existe um assunto com esta descrição.',
        ];
    }
}
