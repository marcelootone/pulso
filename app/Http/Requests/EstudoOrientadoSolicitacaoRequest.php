<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstudoOrientadoSolicitacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'turma_id'             => ['required', 'exists:turmas,id'],
            'disciplina_solicitante' => ['required', 'string', 'max:100'],
            'data_prevista'        => ['required', 'date', 'after_or_equal:today'],
            'descricao'            => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'turma_id.required'             => 'Selecione a turma.',
            'turma_id.exists'               => 'A turma selecionada é inválida.',
            'disciplina_solicitante.required' => 'Informe a disciplina.',
            'data_prevista.required'        => 'Informe a data prevista para a atividade.',
            'data_prevista.after_or_equal'  => 'A data prevista não pode ser anterior a hoje.',
            'descricao.required'            => 'Descreva a atividade.',
            'descricao.min'                 => 'A descrição deve ter ao menos 10 caracteres.',
        ];
    }
}
