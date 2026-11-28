<x-app-layout>
    <x-slot name="header">Agenda</x-slot>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Agenda']
        ]" />
    </x-slot>

    @php
        $antigo = $errors->any() ? [
            'id' => old('evento_id'),
            'titulo' => old('titulo', ''), 'descricao' => old('descricao', ''), 'local' => old('local', ''),
            'data_inicio' => old('data_inicio', $mes->toDateString()), 'data_fim' => old('data_fim', $mes->toDateString()),
            'hora_inicio' => old('hora_inicio', '08:00'), 'hora_fim' => old('hora_fim', '09:00'),
            'dia_inteiro' => filter_var(old('dia_inteiro', false), FILTER_VALIDATE_BOOLEAN),
            'compartilhado' => filter_var(old('compartilhado', false), FILTER_VALIDATE_BOOLEAN),
        ] : null;
        $configuracao = [
            'eventos' => $dadosEventos,
            'salvarUrl' => route('agenda.eventos.store'),
            'hoje' => now()->toDateString(),
            'agendaUrl' => route('agenda.index'),
            'ano' => $mes->year,
            'mes' => $mes->format('m'),
            'antigo' => $antigo,
        ];
    @endphp

    <div x-data="agenda(@js($configuracao))" class="space-y-5">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center rounded-lg border border-gray-200 bg-white">
                    <a href="{{ route('agenda.index', ['mes' => $mes->subMonth()->format('Y-m')]) }}" aria-label="Mês anterior"
                       class="p-2.5 rounded-l-lg text-gray-600 hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-600">
                        <x-icon name="heroicon-o-chevron-left" class="h-5 w-5" />
                    </a>
                    <a href="{{ route('agenda.index') }}" class="px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-600">Hoje</a>
                    <a href="{{ route('agenda.index', ['mes' => $mes->addMonth()->format('Y-m')]) }}" aria-label="Próximo mês"
                       class="p-2.5 rounded-r-lg text-gray-600 hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-600">
                        <x-icon name="heroicon-o-chevron-right" class="h-5 w-5" />
                    </a>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">{{ ucfirst($mes->locale('pt_BR')->translatedFormat('F \d\e Y')) }}</h3>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <x-dropdown align="left" width="w-72" content-classes="bg-white">
                    <x-slot name="trigger">
                        <x-button variant="secondary" x-ref="seletorMes" aria-controls="agenda-seletor-mes" x-bind:aria-expanded="open.toString()"
                                  @click="$nextTick(() => { if (open) $refs.anoAgenda.focus() })">
                            <x-icon name="heroicon-o-calendar" class="w-4 h-4 mr-2" />
                            Ir para o mês
                            <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 ml-2" />
                        </x-button>
                    </x-slot>
                    <x-slot name="content">
                        <div id="agenda-seletor-mes" class="p-4" @click.stop
                             @keydown.escape.prevent.stop="open = false; $refs.seletorMes.focus()">
                            <x-input-label for="agenda-ano" value="Ano" />
                            <x-select id="agenda-ano" x-ref="anoAgenda" x-model="anoSelecionado" class="mt-1 w-full">
                                @foreach (range(max(1, $mes->year - 10), min(9999, $mes->year + 10)) as $ano)
                                    <option value="{{ $ano }}" @selected($ano === $mes->year)>{{ $ano }}</option>
                                @endforeach
                            </x-select>
                            <div role="group" aria-label="Mês" class="mt-4 grid grid-cols-3 gap-2">
                                @foreach (['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'] as $numero => $nomeMes)
                                    @php($numero = str_pad((string) $numero, 2, '0', STR_PAD_LEFT))
                                    <a href="{{ route('agenda.index', ['mes' => $mes->year.'-'.$numero]) }}" x-bind:href="urlMes('{{ $numero }}')"
                                       x-bind:aria-current="Number(anoSelecionado) === {{ $mes->year }} && mesSelecionado === '{{ $numero }}' ? 'date' : null"
                                       x-bind:class="Number(anoSelecionado) === {{ $mes->year }} && mesSelecionado === '{{ $numero }}' ? 'bg-primary-600 text-white hover:bg-primary-700' : 'text-gray-700 hover:bg-gray-100'"
                                       class="rounded-md px-1 py-2.5 text-center text-xs font-medium focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-600">
                                        {{ $nomeMes }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>
                <x-button @click="novo('{{ $mes->isCurrentMonth() ? now()->toDateString() : $mes->toDateString() }}')">
                    <x-icon name="heroicon-o-plus" class="w-4 h-4 mr-2" /> Novo evento
                </x-button>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm">
            <label class="inline-flex items-center gap-2 cursor-pointer text-primary-900">
                <input type="checkbox" x-model="mostrarPessoais" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="w-2.5 h-2.5 rounded-full bg-primary-600" aria-hidden="true"></span> Minha agenda
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer text-emerald-900">
                <input type="checkbox" x-model="mostrarComuns" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600" aria-hidden="true"></span> Eventos comuns
            </label>
            <p class="text-gray-500">Clique em um dia para criar um evento.</p>
        </div>

        <p class="text-xs text-gray-500 sm:hidden">Deslize o calendário para ver todos os dias da semana.</p>
        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm" role="region" aria-label="Calendário mensal" tabindex="0">
            <table class="w-full min-w-[640px] table-fixed border-collapse">
                <caption class="sr-only">Agenda de {{ $mes->locale('pt_BR')->translatedFormat('F \d\e Y') }}</caption>
                <thead>
                    <tr>
                        @foreach (['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'] as $semana)
                            <th scope="col" class="border-b border-gray-200 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <abbr title="{{ $semana }}" class="no-underline">{{ mb_substr($semana, 0, 3) }}</abbr>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach (array_chunk($eventosPorDia, 7, true) as $semana)
                        <tr>
                            @foreach ($semana as $data => $eventos)
                                @php($dia = \Carbon\CarbonImmutable::parse($data))
                                <td @class(['group relative h-36 align-top border-b border-r border-gray-100 last:border-r-0', 'bg-gray-50' => $dia->month !== $mes->month])>
                                    <button type="button" @click="novo('{{ $data }}')" aria-label="Adicionar evento em {{ $dia->format('d/m/Y') }}"
                                            class="absolute inset-0 h-full w-full hover:bg-primary-50/40 focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-primary-600"></button>
                                    <div class="pointer-events-none relative p-2">
                                        <div class="mb-1 flex items-center justify-between">
                                            <span @class(['inline-flex h-7 w-7 items-center justify-center rounded-full text-sm font-semibold', 'bg-primary-600 text-white' => $dia->isToday(), 'text-gray-400' => ! $dia->isToday() && $dia->month !== $mes->month, 'text-gray-700' => ! $dia->isToday() && $dia->month === $mes->month])
                                                  @if($dia->isToday()) aria-current="date" @endif>{{ $dia->day }}</span>
                                            <x-icon name="heroicon-o-plus" class="w-4 h-4 text-gray-400 opacity-0 group-hover:opacity-100" />
                                        </div>
                                        <div class="space-y-1">
                                            @foreach ($eventos as $evento)
                                                <button type="button" @click.stop="editar({{ $evento->id }})"
                                                        x-show="{{ $evento->compartilhado ? 'mostrarComuns' : 'mostrarPessoais' }}"
                                                        aria-label="{{ $dadosEventos[$evento->id]['pode_editar'] ? 'Editar' : 'Ver' }} evento {{ $evento->titulo }}"
                                                        title="{{ $evento->titulo }} · {{ $evento->compartilhado ? 'Evento comum' : 'Pessoal' }}"
                                                        @class(['pointer-events-auto relative block w-full truncate rounded px-2 py-1 text-left text-xs font-medium focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1', 'bg-emerald-100 text-emerald-900 hover:bg-emerald-200 focus-visible:outline-emerald-600' => $evento->compartilhado, 'bg-primary-100 text-primary-900 hover:bg-primary-200 focus-visible:outline-primary-600' => ! $evento->compartilhado])>
                                                    <span class="font-semibold">{{ $evento->dia_inteiro ? 'Dia inteiro' : ($evento->inicio->toDateString() === $data ? $evento->inicio->format('H:i') : 'Continuação') }}</span>
                                                    {{ $evento->titulo }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-modal name="agenda-evento" :show="$errors->any()" maxWidth="2xl">
            <section role="dialog" aria-modal="true" aria-labelledby="agenda-evento-titulo" x-trap.inert.noscroll="show" class="p-6">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h3 id="agenda-evento-titulo" class="text-xl font-bold text-gray-900" x-text="formulario.id ? (podeEditar ? 'Editar evento' : 'Detalhes do evento') : 'Novo evento'"></h3>
                        <p class="mt-1 text-sm text-gray-500" x-show="formulario.autor" x-text="'Criado por ' + formulario.autor"></p>
                    </div>
                    <button type="button" @click="$dispatch('close-modal', 'agenda-evento')" aria-label="Fechar evento" class="rounded p-1 text-gray-500 hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-600">
                        <x-icon name="heroicon-o-x-mark" class="h-5 w-5" />
                    </button>
                </div>

                @if ($errors->any())
                    <div role="alert" class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-800">
                        <p class="font-semibold">Confira os dados do evento:</p>
                        <ul class="mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $erro)
                                <li>{{ $erro }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="agenda-evento-form" method="POST" :action="formulario.id ? formulario.update_url : salvarUrl" @submit="enviando = true" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" :value="formulario.id ? 'PUT' : 'POST'">
                    <input type="hidden" name="evento_id" :value="formulario.id || ''">
                    <input type="hidden" name="dia_inteiro" :value="formulario.dia_inteiro ? '1' : '0'">
                    <input type="hidden" name="compartilhado" :value="formulario.compartilhado ? '1' : '0'">

                    <fieldset :disabled="!podeEditar" class="space-y-4">
                        <legend class="sr-only">Dados do evento</legend>
                        <div>
                            <x-input-label for="evento-titulo" value="Título *" />
                            <x-input id="evento-titulo" name="titulo" x-model="formulario.titulo" required maxlength="255" class="mt-1 w-full" aria-invalid="{{ $errors->has('titulo') ? 'true' : 'false' }}" />
                        </div>
                        @can('gerenciarComuns', \App\Models\EventoAgenda::class)
                            <div>
                                <x-input-label for="evento-visibilidade" value="Agenda" />
                                <x-select id="evento-visibilidade" x-model.boolean="formulario.compartilhado" x-bind:disabled="!podeEditar || (formulario.id && !formulario.proprio)" class="mt-1 w-full">
                                    <option value="false">Pessoal — somente eu</option>
                                    <option value="true">Comum — todos os usuários</option>
                                </x-select>
                            </div>
                        @else
                            <p class="text-sm text-gray-600" x-text="formulario.compartilhado ? 'Evento comum, visível para todos os usuários.' : 'Evento pessoal, visível somente para você.'"></p>
                        @endcan
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" x-model="formulario.dia_inteiro" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"> Dia inteiro
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="evento-data-inicio" value="Data de início *" />
                                <x-input id="evento-data-inicio" name="data_inicio" type="date" x-model="formulario.data_inicio" @change="ajustarDataFim()" required class="mt-1 w-full" aria-invalid="{{ $errors->has('data_inicio') ? 'true' : 'false' }}" />
                            </div>
                            <div>
                                <x-input-label for="evento-data-fim" value="Data de término *" />
                                <x-input id="evento-data-fim" name="data_fim" type="date" x-model="formulario.data_fim" x-bind:min="formulario.data_inicio" required class="mt-1 w-full" aria-invalid="{{ $errors->has('data_fim') ? 'true' : 'false' }}" />
                            </div>
                        </div>
                        <div x-show="!formulario.dia_inteiro" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="evento-hora-inicio" value="Hora de início *" />
                                <x-input id="evento-hora-inicio" name="hora_inicio" type="time" x-model="formulario.hora_inicio" x-bind:required="!formulario.dia_inteiro" class="mt-1 w-full" aria-invalid="{{ $errors->has('hora_inicio') ? 'true' : 'false' }}" />
                            </div>
                            <div>
                                <x-input-label for="evento-hora-fim" value="Hora de término *" />
                                <x-input id="evento-hora-fim" name="hora_fim" type="time" x-model="formulario.hora_fim" x-bind:required="!formulario.dia_inteiro" class="mt-1 w-full" aria-invalid="{{ $errors->has('hora_fim') ? 'true' : 'false' }}" />
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Horários de Brasília.</p>
                        <div>
                            <x-input-label for="evento-local" value="Local" />
                            <x-input id="evento-local" name="local" x-model="formulario.local" maxlength="255" class="mt-1 w-full" />
                        </div>
                        <div>
                            <x-input-label for="evento-descricao" value="Descrição" />
                            <textarea id="evento-descricao" name="descricao" x-model="formulario.descricao" maxlength="5000" rows="3" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        </div>
                    </fieldset>
                </form>

                <div x-show="!confirmandoExclusao" class="mt-6 flex flex-wrap justify-end gap-3">
                    <x-button variant="danger" x-show="formulario.id && podeEditar" @click="confirmandoExclusao = true" x-bind:disabled="enviando" class="sm:mr-auto">Excluir</x-button>
                    <x-button variant="secondary" @click="$dispatch('close-modal', 'agenda-evento')" x-bind:disabled="enviando">Fechar</x-button>
                    <x-button type="submit" form="agenda-evento-form" x-show="podeEditar" x-bind:disabled="enviando" x-text="enviando ? 'Salvando…' : 'Salvar evento'">Salvar evento</x-button>
                </div>
                <form method="POST" :action="formulario.delete_url" x-show="confirmandoExclusao" @submit="enviando = true" class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4">
                    @csrf
                    @method('DELETE')
                    <p class="text-sm font-medium text-red-800">Excluir este evento? Esta ação não pode ser desfeita.</p>
                    <div class="mt-3 flex flex-wrap justify-end gap-3">
                        <x-button variant="secondary" @click="confirmandoExclusao = false" x-bind:disabled="enviando">Cancelar</x-button>
                        <x-button type="submit" variant="danger" x-bind:disabled="enviando" x-text="enviando ? 'Excluindo…' : 'Confirmar exclusão'">Confirmar exclusão</x-button>
                    </div>
                </form>
            </section>
        </x-modal>
    </div>
</x-app-layout>
