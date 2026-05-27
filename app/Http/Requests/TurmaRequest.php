<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TurmaRequest extends FormRequest
{
    /**
     * Qualquer funcionário autenticado pode criar/editar turmas
     * (o controle fino é feito pelo middleware 'restrito' na rota).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação compartilhadas entre store() e update().
     */
    public function rules(): array
    {
        return [
            'modalidade'  => ['required', 'string', 'max:100'],
            'turno'       => ['required', 'string', 'in:Matutino,Vespertino,Noturno,Integral'],
            'serie'       => ['required', 'string', 'max:10'],
            'complemento' => ['nullable', 'string', 'max:3'],
            'ano_letivo'  => ['nullable', 'integer', 'min:2000', 'max:2099'],
            'tipo'        => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'modalidade.required' => 'A modalidade de ensino é obrigatória.',
            'turno.required'      => 'O turno é obrigatório.',
            'turno.in'            => 'O turno deve ser Matutino, Vespertino, Noturno ou Integral.',
            'serie.required'      => 'A série é obrigatória.',
            'complemento.max'     => 'O complemento não pode ter mais de 3 caracteres.',
            'ano_letivo.integer'  => 'O ano letivo deve ser um número válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'modalidade'  => 'Modalidade',
            'turno'       => 'Turno',
            'serie'       => 'Série',
            'complemento' => 'Complemento',
            'ano_letivo'  => 'Ano Letivo',
        ];
    }
}
