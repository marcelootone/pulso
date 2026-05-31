<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EletivaRequest extends FormRequest
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
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:eletiva,clube',
            'vagas' => 'required|integer|min:1',
            'usa_nota' => 'boolean',
            'ano_letivo' => 'required|integer|digits:4|min:2020|max:2099',
            'professor_ids' => 'required|array|min:1',
            'professor_ids.*' => 'exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'tipo.required' => 'O tipo é obrigatório.',
            'vagas.required' => 'O número de vagas é obrigatório.',
            'vagas.min' => 'O número de vagas deve ser no mínimo 1.',
            'ano_letivo.required' => 'O ano letivo é obrigatório.',
            'ano_letivo.digits' => 'O ano letivo deve ter 4 dígitos.',
            'ano_letivo.max' => 'O ano letivo deve ser no máximo 2099.',
            'professor_ids.required' => 'Selecione pelo menos um professor responsável.',
            'professor_ids.min' => 'Selecione pelo menos um professor responsável.',
            'professor_ids.*.exists' => 'Um dos professores selecionados é inválido.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                $eletiva = $this->route('eletiva');
                if ($eletiva) {
                    $alunosInscritos = $eletiva->alunosAtivos->count();
                    if ($this->vagas < $alunosInscritos) {
                        $validator->errors()->add('vagas', "A capacidade não pode ser menor que o número de alunos já inscritos ($alunosInscritos).");
                    }
                }
            }
        });
    }
}
