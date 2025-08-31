<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLivroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Titulo' => 'required|string|max:40',
            'Editora' => 'required|string|max:40',
            'Edicao' => 'required|integer|min:1',
            'AnoPublicacao' => 'required|string|size:4',
            'Valor' => 'required|numeric|min:0|max:999999.99',
            'autores' => 'array',
            'autores.*' => 'exists:autores,CodAu',
            'assuntos' => 'array',
            'assuntos.*' => 'exists:assuntos,codAs',
        ];
    }

    public function messages(): array
    {
        return [
            'Titulo.required' => 'O título do livro é obrigatório.',
            'Titulo.max' => 'O título não pode ter mais de 40 caracteres.',
            'Editora.required' => 'A editora é obrigatória.',
            'Editora.max' => 'A editora não pode ter mais de 40 caracteres.',
            'Edicao.required' => 'A edição é obrigatória.',
            'Edicao.integer' => 'A edição deve ser um número inteiro.',
            'Edicao.min' => 'A edição deve ser no mínimo 1.',
            'AnoPublicacao.required' => 'O ano de publicação é obrigatório.',
            'AnoPublicacao.size' => 'O ano de publicação deve ter exatamente 4 caracteres.',
            'Valor.required' => 'O valor é obrigatório.',
            'Valor.numeric' => 'O valor deve ser um número.',
            'Valor.min' => 'O valor deve ser no mínimo 0.',
            'Valor.max' => 'O valor não pode ser maior que 999999.99.',
            'autores.*.exists' => 'Um ou mais autores selecionados não existem.',
            'assuntos.*.exists' => 'Um ou mais assuntos selecionados não existem.',
        ];
    }
}
