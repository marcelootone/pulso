<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EstudoOrientadoEvolucaoRequest extends FormRequest
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
            'data_registro' => ['required', 'date'],
            'descricao' => ['required', 'string', 'min:10'],
            'indicador' => ['required', 'in:Melhora,Estavel,Piora'],
        ];
    }

    public function messages(): array
    {
        return [
            'data_registro.required' => 'A data do registro é obrigatória.',
            'data_registro.date' => 'A data informada é inválida.',
            'descricao.required' => 'A descrição da evolução é obrigatória.',
            'descricao.min' => 'A descrição deve ter no mínimo 10 caracteres.',
            'indicador.required' => 'Selecione um indicador de evolução.',
            'indicador.in' => 'Indicador inválido.',
        ];
    }
}
