<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EstudoOrientadoAtendimentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data_atendimento' => ['required', 'date'],
            'descricao' => ['required', 'string', 'min:10'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'data_atendimento.required' => 'A data do atendimento é obrigatória.',
            'data_atendimento.date' => 'A data informada é inválida.',
            'descricao.required' => 'A descrição do atendimento é obrigatória.',
            'descricao.min' => 'A descrição deve ter no mínimo 10 caracteres.',
        ];
    }
}
