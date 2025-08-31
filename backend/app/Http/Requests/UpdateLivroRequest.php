<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLivroRequest extends FormRequest
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
        return [
            'Titulo' => 'sometimes|required|string|max:40',
            'Editora' => 'sometimes|required|string|max:40',
            'Edicao' => 'sometimes|required|integer|min:1',
            'AnoPublicacao' => 'sometimes|required|string|size:4|regex:/^\d{4}$/|before_or_equal:' . date('Y'),
            'Valor' => 'sometimes|required|numeric|min:0|max:99999999.99',
            'autores' => 'sometimes|required|array|min:1',
            'autores.*' => 'required|integer|exists:autores,CodAu',
            'assuntos' => 'sometimes|nullable|array',
            'assuntos.*' => 'integer|exists:assuntos,codAs',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'Titulo.required' => 'O título do livro é obrigatório.',
            'Titulo.max' => 'O título não pode ter mais de 40 caracteres.',
            'Editora.required' => 'A editora é obrigatória.',
            'Editora.max' => 'A editora não pode ter mais de 40 caracteres.',
            'Edicao.required' => 'A edição é obrigatória.',
            'Edicao.integer' => 'A edição deve ser um número inteiro.',
            'Edicao.min' => 'A edição deve ser maior que zero.',
            'AnoPublicacao.required' => 'O ano de publicação é obrigatório.',
            'AnoPublicacao.size' => 'O ano de publicação deve ter 4 dígitos.',
            'AnoPublicacao.regex' => 'O ano de publicação deve conter apenas números.',
            'AnoPublicacao.before_or_equal' => 'O ano de publicação não pode ser superior ao ano atual.',
            'Valor.required' => 'O valor do livro é obrigatório.',
            'Valor.numeric' => 'O valor deve ser um número.',
            'Valor.min' => 'O valor deve ser maior ou igual a zero.',
            'Valor.max' => 'O valor não pode ser superior a R$ 99.999.999,99.',
            'autores.required' => 'É obrigatório selecionar pelo menos um autor.',
            'autores.min' => 'É obrigatório selecionar pelo menos um autor.',
            'autores.*.exists' => 'Um ou mais autores selecionados não existem.',
            'assuntos.*.exists' => 'Um ou mais assuntos selecionados não existem.',
        ];
    }
}
