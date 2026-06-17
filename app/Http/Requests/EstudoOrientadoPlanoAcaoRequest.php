<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EstudoOrientadoPlanoAcaoRequest extends FormRequest
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
            'descricao' => ['required', 'string', 'min:10'],
            'metas' => ['nullable', 'string'],
            'estrategias' => ['nullable', 'string'],
            'prazo' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'descricao.required' => 'A descrição do plano de ação é obrigatória.',
            'descricao.min' => 'A descrição deve ter no mínimo 10 caracteres.',
            'prazo.date' => 'A data informada é inválida.',
            'prazo.after_or_equal' => 'O prazo não pode ser uma data no passado.',
        ];
    }
}
