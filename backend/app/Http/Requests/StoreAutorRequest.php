<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutorRequest extends FormRequest
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
            'Nome' => 'required|string|max:40|unique:autores,Nome',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'Nome.required' => 'O nome do autor é obrigatório.',
            'Nome.max' => 'O nome do autor não pode ter mais de 40 caracteres.',
            'Nome.unique' => 'Já existe um autor com este nome.',
        ];
    }
}
