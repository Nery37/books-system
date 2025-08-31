<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssuntoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Descricao' => 'required|string|max:20|unique:assuntos,Descricao',
        ];
    }

    public function messages(): array
    {
        return [
            'Descricao.required' => 'A descrição do assunto é obrigatória.',
            'Descricao.max' => 'A descrição do assunto não pode ter mais de 20 caracteres.',
            'Descricao.unique' => 'Já existe um assunto com esta descrição.',
        ];
    }
}
