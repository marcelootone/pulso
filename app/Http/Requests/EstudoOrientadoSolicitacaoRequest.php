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
            'aluno_id'             => ['required', 'exists:alunos,id'],
            'turma_id'             => ['required', 'exists:turmas,id'],
            'disciplina_solicitante' => ['required', 'string', 'max:100'],
            'prioridade'           => ['required', 'in:Baixa,Media,Alta'],
            'motivo'               => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'aluno_id.required'             => 'Selecione o aluno.',
            'aluno_id.exists'               => 'O aluno selecionado é inválido.',
            'turma_id.required'             => 'Selecione a turma.',
            'turma_id.exists'               => 'A turma selecionada é inválida.',
            'disciplina_solicitante.required' => 'Informe a disciplina.',
            'prioridade.required'           => 'Informe a prioridade.',
            'prioridade.in'                 => 'Prioridade inválida.',
            'motivo.required'               => 'Descreva o motivo do encaminhamento.',
            'motivo.min'                    => 'O motivo deve ter ao menos 10 caracteres.',
        ];
    }
}
