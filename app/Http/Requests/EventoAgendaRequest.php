<?php

namespace App\Http\Requests;

use App\Models\EventoAgenda;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class EventoAgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $evento = $this->route('evento');
        $user = $this->user();

        if ($evento && ! $user->can('update', $evento)) {
            return false;
        }

        if ($this->boolean('compartilhado') && ! $user->can('gerenciarComuns', EventoAgenda::class)) {
            return false;
        }

        // Tornar um evento comum pessoal só pode afetar a agenda do próprio autor.
        return ! $evento || ! $evento->compartilhado || $this->boolean('compartilhado')
            || (int) $evento->user_id === (int) $user->id;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:5000'],
            'local' => ['nullable', 'string', 'max:255'],
            'data_inicio' => ['required', 'string', 'date_format:Y-m-d'],
            'data_fim' => ['required', 'string', 'date_format:Y-m-d'],
            'dia_inteiro' => ['required', 'boolean'],
            'compartilhado' => ['required', 'boolean'],
            'hora_inicio' => [Rule::excludeIf($this->boolean('dia_inteiro')), 'required', 'date_format:H:i'],
            'hora_fim' => [Rule::excludeIf($this->boolean('dia_inteiro')), 'required', 'date_format:H:i'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if ($this->input('data_fim') < $this->input('data_inicio')) {
                $validator->errors()->add('data_fim', 'A data de término não pode ser anterior à data de início.');

                return;
            }

            [$inicio, $fim] = $this->intervalo();
            if ($fim->lessThanOrEqualTo($inicio)) {
                $validator->errors()->add('hora_fim', 'O término deve ser posterior ao início do evento.');
            }
        }];
    }

    public function dadosEvento(): array
    {
        [$inicio, $fim] = $this->intervalo();

        return $this->safe()->only(['titulo', 'descricao', 'local']) + [
            'inicio' => $inicio,
            'fim' => $fim,
            'dia_inteiro' => $this->boolean('dia_inteiro'),
            'compartilhado' => $this->boolean('compartilhado'),
        ];
    }

    private function intervalo(): array
    {
        $diaInteiro = $this->boolean('dia_inteiro');
        $inicio = CarbonImmutable::createFromFormat('!Y-m-d H:i', $this->input('data_inicio').' '.($diaInteiro ? '00:00' : $this->input('hora_inicio')), config('app.timezone'));
        $fim = CarbonImmutable::createFromFormat('!Y-m-d H:i', $this->input('data_fim').' '.($diaInteiro ? '00:00' : $this->input('hora_fim')), config('app.timezone'));

        return [$inicio, $diaInteiro ? $fim->addDay() : $fim];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'max.string' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'date_format' => 'Informe um valor válido para :attribute.',
            'boolean' => 'Selecione uma opção válida para :attribute.',
        ];
    }

    public function attributes(): array
    {
        return [
            'titulo' => 'título', 'descricao' => 'descrição', 'local' => 'local',
            'data_inicio' => 'data de início', 'data_fim' => 'data de término',
            'hora_inicio' => 'hora de início', 'hora_fim' => 'hora de término',
            'dia_inteiro' => 'dia inteiro', 'compartilhado' => 'visibilidade',
        ];
    }

    protected function getRedirectUrl(): string
    {
        $data = $this->input('data_inicio');
        $mes = is_string($data) && preg_match('/^\d{4}-(0[1-9]|1[0-2])-\d{2}$/', $data)
            ? substr($data, 0, 7) : now()->format('Y-m');

        return route('agenda.index', ['mes' => $mes]);
    }
}
