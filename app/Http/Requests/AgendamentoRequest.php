<?php

namespace App\Http\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AgendamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $agendamento = $this->route('agendamento');

        return ! $agendamento || $this->user()->can('update', $agendamento);
    }

    public function periodoPermitido(): array
    {
        $hoje = CarbonImmutable::today(config('app.timezone'));

        return [$hoje, $hoje->addMonthNoOverflow()];
    }

    protected function prepareForValidation(): void
    {
        if ($this->isMethod('GET') && ! $this->has('data')) {
            $this->merge(['data' => $this->periodoPermitido()[0]->toDateString()]);
        }
    }

    public function rules(): array
    {
        [$inicio, $fim] = $this->periodoPermitido();
        $rules = [
            'data' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$inicio->toDateString(), 'before_or_equal:'.$fim->toDateString()],
        ];

        if ($this->isMethod('POST') || $this->isMethod('PUT')) {
            $rules += [
                'horario_inicio' => 'required|date_format:H:i',
                'horario_fim' => 'required|date_format:H:i|after:horario_inicio',
                'motivo' => 'nullable|string|max:255',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'data.required' => 'Informe a data da reserva.',
            'data.date_format' => 'Informe uma data válida para a reserva.',
            'data.after_or_equal' => 'A reserva não pode ser feita para uma data passada.',
            'data.before_or_equal' => 'Você pode agendar até '.$this->periodoPermitido()[1]->format('d/m/Y').'.',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if (! $this->isMethod('PUT') || $validator->errors()->isNotEmpty()) {
                return;
            }

            $inicio = CarbonImmutable::parse($this->input('data').' '.$this->input('horario_inicio'), config('app.timezone'));
            if (! $inicio->isFuture()) {
                $validator->errors()->add('horario_inicio', 'O novo horário de início deve ser posterior ao horário atual.');
            }
        }];
    }

    protected function getRedirectUrl(): string
    {
        if ($this->route('agendamento')) {
            return route('agendamentos.edit', $this->route('agendamento'));
        }

        // Retorna ao formulário sem a data inválida, evitando ciclos de redirecionamento.
        return route('agendamentos.create', ['espaco' => $this->route('espaco')]);
    }
}
