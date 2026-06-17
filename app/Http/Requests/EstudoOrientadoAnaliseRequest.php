<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EstudoOrientadoAnaliseRequest extends FormRequest
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
            'acao' => ['required', 'in:aprovar,rejeitar,atribuir'],
            'parecer' => ['required_if:acao,aprovar,rejeitar', 'nullable', 'string', 'min:5'],
            'professor_orientador_id' => ['required_if:acao,atribuir', 'nullable', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'acao.required' => 'Ação inválida.',
            'acao.in' => 'Ação inválida.',
            'parecer.required_if' => 'O parecer é obrigatório.',
            'parecer.min' => 'O parecer deve ter no mínimo 5 caracteres.',
            'professor_orientador_id.required_if' => 'Selecione um professor orientador.',
            'professor_orientador_id.exists' => 'O professor orientador selecionado é inválido.',
        ];
    }
}
