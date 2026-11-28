<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlunoRequest extends FormRequest
{
    protected $redirectRoute = 'alunos.create';

    public function authorize(): bool
    {
        return $this->user()->can('cadastrar alunos');
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'ra' => ['required', 'string', 'max:255', Rule::unique('alunos', 'ra')],
            'nascimento' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'sexo' => ['nullable', Rule::in(['M', 'F'])],
            'telefone' => ['nullable', 'string', 'max:255'],
            'nome_mae' => ['nullable', 'string', 'max:255'],
            'telefone_responsavel' => ['nullable', 'string', 'max:255'],
            'cep' => ['nullable', 'string', 'max:9', 'regex:/^\d{5}-?\d{3}$/'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'status_matricula' => ['required', Rule::in(['Ativo', 'Novato', 'Transferência', 'Deixou de frequentar', 'Falecimento'])],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'max.string' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'ra.unique' => 'Já existe um aluno cadastrado com este RA.',
            'nascimento.date_format' => 'Informe uma data de nascimento válida.',
            'nascimento.before_or_equal' => 'A data de nascimento não pode estar no futuro.',
            'sexo.in' => 'Selecione uma opção válida para sexo.',
            'cep.regex' => 'Informe um CEP válido com 8 dígitos, com ou sem hífen.',
            'status_matricula.in' => 'Selecione um status válido para o aluno.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome completo',
            'ra' => 'RA',
            'nascimento' => 'data de nascimento',
            'telefone' => 'telefone do estudante',
            'nome_mae' => 'nome da mãe',
            'telefone_responsavel' => 'telefone do responsável',
            'cep' => 'CEP',
            'logradouro' => 'logradouro',
            'status_matricula' => 'status do aluno',
        ];
    }
}
