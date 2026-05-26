<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Planejamento Semanal') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Mensagens de feedback --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="mb-6 bg-emerald-50 border border-emerald-300 text-emerald-800 px-5 py-3 rounded-lg shadow-sm flex items-center" id="feedback-success">
                    <svg class="h-5 w-5 mr-2 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-300 text-red-800 px-5 py-3 rounded-lg shadow-sm flex items-center">
                    <svg class="h-5 w-5 mr-2 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Cabeçalho do planejamento --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200">
                
                {{-- Título e navegação semanal --}}
                <div class="bg-gradient-to-r from-blue-700 to-blue-600 px-6 py-5 rounded-t-xl">
                    <h1 class="text-xl font-bold text-black tracking-wide text-center uppercase mb-4" id="titulo-planejamento">
                        Rotina de Planejamento Pedagógico
                    </h1>
                    <div class="flex items-center justify-center gap-4">
                        <a href="{{ route('planejamento.index', ['data' => $semanaAnterior->toDateString()]) }}"
                           class="bg-white/20 hover:bg-white/30 text-black rounded-lg px-3 py-2 transition-colors duration-200 flex items-center gap-1"
                           id="btn-semana-anterior" title="Semana anterior">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span class="text-sm font-medium hidden sm:inline">Anterior</span>
                        </a>
                        <div class="bg-white/10 rounded-lg px-5 py-2 border border-white/20" id="periodo-semana">
                            <span class="text-black font-semibold text-base">
                                {{ $planejamento->semana_inicio->format('d/m/Y') }}
                                até
                                {{ $planejamento->semana_fim->format('d/m/Y') }}
                            </span>
                        </div>
                        <a href="{{ route('planejamento.index', ['data' => $proximaSemana->toDateString()]) }}"
                           class="bg-white/20 hover:bg-white/30 text-black rounded-lg px-3 py-2 transition-colors duration-200 flex items-center gap-1"
                           id="btn-proxima-semana" title="Próxima semana">
                            <span class="text-sm font-medium hidden sm:inline">Próxima</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Formulário principal --}}
                <form action="{{ route('planejamento.salvar') }}" method="POST" id="form-planejamento">
                    @csrf
                    <input type="hidden" name="planejamento_id" value="{{ $planejamento->id }}">

                    {{-- Tabela --}}
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse min-w-[1100px]" id="tabela-planejamento">
                            <thead>
                                <tr class="bg-blue-50">
                                    <th class="border border-gray-300 px-1 py-3 text-sm font-bold text-blue-800 w-10 text-center">
                                        {{-- Drag --}}
                                    </th>
                                    <th class="border border-gray-300 px-3 py-3 text-sm font-bold text-blue-800 w-32 text-center">
                                        Horário
                                    </th>
                                    @foreach($diasSemana as $dia)
                                        <th class="border border-gray-300 px-3 py-3 text-sm font-bold text-blue-800 text-center">
                                            {{ $diasLabels[$dia] }}
                                        </th>
                                    @endforeach
                                    <th class="border border-gray-300 px-2 py-3 text-sm font-bold text-blue-800 w-12 text-center">
                                        {{-- Ações --}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($planejamento->horarios as $index => $horario)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50/30 transition-colors duration-150" data-horario-id="{{ $horario->id }}">
                                        {{-- Coluna Drag Handle --}}
                                        <td class="border border-gray-300 px-1 py-3 align-middle text-center cursor-move drag-handle text-gray-400 hover:text-blue-600 transition-colors" title="Arraste para reordenar">
                                            <svg class="h-6 w-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                            </svg>
                                        </td>
                                        {{-- Coluna Horário --}}
                                        <td class="border border-gray-300 px-2 py-3 align-top">
                                            <div class="flex flex-col gap-1.5">
                                                <label class="text-xs text-gray-500 font-medium">Início</label>
                                                <input type="time"
                                                       name="horarios[{{ $horario->id }}][horario_inicio]"
                                                       value="{{ $horario->horario_inicio }}"
                                                       class="block w-full rounded border-gray-300 text-sm py-1.5 px-2 focus:ring-blue-500 focus:border-blue-500"
                                                       id="horario-inicio-{{ $horario->id }}">
                                                <label class="text-xs text-gray-500 font-medium">Fim</label>
                                                <input type="time"
                                                       name="horarios[{{ $horario->id }}][horario_fim]"
                                                       value="{{ $horario->horario_fim }}"
                                                       class="block w-full rounded border-gray-300 text-sm py-1.5 px-2 focus:ring-blue-500 focus:border-blue-500"
                                                       id="horario-fim-{{ $horario->id }}">
                                            </div>
                                        </td>

                                        {{-- Colunas dos dias --}}
                                        @foreach($diasSemana as $dia)
                                            @php
                                                $item = $horario->itemDoDia($dia);
                                            @endphp
                                            <td class="border border-gray-300 px-2 py-2 align-top">
                                                <div class="flex flex-col gap-1.5">
                                                    {{-- Tarefa --}}
                                                    <div>
                                                        <label class="text-xs text-gray-500 font-medium">Tarefa</label>
                                                        <input type="text"
                                                               name="horarios[{{ $horario->id }}][itens][{{ $dia }}][tarefa]"
                                                               value="{{ $item?->tarefa }}"
                                                               placeholder="Ex: PCA, Tutoria..."
                                                               class="block w-full rounded border-gray-300 text-sm py-1.5 px-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                                                               id="tarefa-{{ $horario->id }}-{{ $dia }}">
                                                    </div>

                                                    {{-- Andamento --}}
                                                    <div>
                                                        <label class="text-xs text-gray-500 font-medium">Andamento</label>
                                                        <select name="horarios[{{ $horario->id }}][itens][{{ $dia }}][andamento]"
                                                                class="block w-full rounded border-gray-300 text-sm py-1.5 px-2 focus:ring-blue-500 focus:border-blue-500
                                                                    {{ $item?->andamento === 'CONCLUIDO' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                                                    {{ $item?->andamento === 'EM_ANDAMENTO' ? 'bg-amber-50 text-amber-700' : '' }}
                                                                    {{ $item?->andamento === 'NAO_CONCLUIDO' ? 'bg-red-50 text-red-700' : '' }}"
                                                                onchange="atualizarCorAndamento(this)"
                                                                id="andamento-{{ $horario->id }}-{{ $dia }}">
                                                            @foreach($andamentoOptions as $value => $label)
                                                                <option value="{{ $value }}" {{ ($item?->andamento ?? '') === $value ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- Observação --}}
                                                    <div>
                                                        <label class="text-xs text-gray-500 font-medium">Observação</label>
                                                        <textarea name="horarios[{{ $horario->id }}][itens][{{ $dia }}][observacao]"
                                                                  rows="2"
                                                                  placeholder="Observações..."
                                                                  class="block w-full rounded border-gray-300 text-sm py-1.5 px-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400 resize-none"
                                                                  id="observacao-{{ $horario->id }}-{{ $dia }}">{{ $item?->observacao }}</textarea>
                                                    </div>
                                                </div>
                                            </td>
                                        @endforeach

                                        {{-- Coluna de ação: Remover --}}
                                        <td class="border border-gray-300 px-1 py-2 align-middle text-center">
                                            <button type="button"
                                                    onclick="confirmarRemocaoHorario({{ $horario->id }})"
                                                    class="text-red-400 hover:text-red-600 hover:bg-red-50 rounded p-1.5 transition-colors duration-200"
                                                    title="Remover horário"
                                                    id="btn-remover-{{ $horario->id }}">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($diasSemana) + 3 }}" class="border border-gray-300 px-4 py-8 text-center text-gray-500 italic">
                                            Nenhum horário cadastrado para esta semana.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Barra de ações fixa --}}
                    <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 rounded-b-xl">
                        {{-- Botão Criar Horário --}}
                        <div x-data="{ open: false }" class="relative">
                            <button type="button"
                                    @click="open = !open"
                                    class="bg-blue-600 hover:bg-blue-700 text-black font-semibold py-2.5 px-5 rounded-lg shadow-sm transition-colors duration-200 flex items-center gap-2"
                                    id="btn-criar-horario">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Criar Horário
                            </button>

                            {{-- Dropdown para novo horário --}}
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute bottom-[110%] left-0 bg-white rounded-xl shadow-xl border border-gray-200 p-5 w-72 z-50 origin-bottom-left"
                                 id="dropdown-criar-horario">
                                <h3 class="text-sm font-bold text-gray-700 mb-3">Novo Horário</h3>
                                <div class="flex gap-3 mb-4">
                                    <div>
                                        <label class="text-xs text-gray-500 font-medium block mb-1">Início</label>
                                        <input type="time" id="novo-horario-inicio" value="08:00"
                                               class="w-full rounded border-gray-300 text-sm py-1.5 px-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 font-medium block mb-1">Fim</label>
                                        <input type="time" id="novo-horario-fim" value="08:50"
                                               class="w-full rounded border-gray-300 text-sm py-1.5 px-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                <button type="button"
                                        onclick="adicionarNovoHorario()"
                                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-black font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors duration-200 text-sm"
                                        id="btn-confirmar-horario">
                                    Adicionar
                                </button>
                            </div>
                        </div>

                        {{-- Botão Salvar --}}
                        <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-black font-semibold py-2.5 px-6 rounded-lg shadow-sm transition-colors duration-200 flex items-center gap-2"
                                id="btn-salvar">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

            {{-- Voltar ao dashboard --}}
            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar ao Painel
                </a>
            </div>
        </div>
    </div>

    {{-- Formulário oculto para remover horário --}}
    <form id="form-remover-horario" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Formulário oculto para adicionar horário --}}
    <form id="form-adicionar-horario" action="{{ route('planejamento.adicionar-horario') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="planejamento_id" value="{{ $planejamento->id }}">
        <input type="hidden" name="horario_inicio" id="input-novo-inicio">
        <input type="hidden" name="horario_fim" id="input-novo-fim">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tbody = document.querySelector('#tabela-planejamento tbody');
            if (tbody) {
                new Sortable(tbody, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'bg-blue-100',
                    onEnd: function () {
                        const rows = tbody.querySelectorAll('tr[data-horario-id]');
                        const ids = Array.from(rows).map(row => row.getAttribute('data-horario-id'));
                        
                        fetch('{{ route("planejamento.reordenar") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                planejamento_id: {{ $planejamento->id }},
                                horarios: ids
                            })
                        }).then(response => {
                            if (!response.ok) {
                                alert('Erro ao reordenar horários.');
                            }
                        }).catch(error => {
                            console.error('Erro na reordenação:', error);
                            alert('Erro na conexão ao reordenar.');
                        });
                    }
                });
            }
        });
        /**
         * Atualiza a cor de fundo do select de andamento conforme o valor selecionado.
         */
        function atualizarCorAndamento(select) {
            // Remove todas as classes de cor
            select.classList.remove(
                'bg-emerald-50', 'text-emerald-700',
                'bg-amber-50', 'text-amber-700',
                'bg-red-50', 'text-red-700'
            );

            // Aplica a classe correspondente
            switch (select.value) {
                case 'CONCLUIDO':
                    select.classList.add('bg-emerald-50', 'text-emerald-700');
                    break;
                case 'EM_ANDAMENTO':
                    select.classList.add('bg-amber-50', 'text-amber-700');
                    break;
                case 'NAO_CONCLUIDO':
                    select.classList.add('bg-red-50', 'text-red-700');
                    break;
            }
        }

        /**
         * Confirma e envia a remoção de um horário.
         */
        function confirmarRemocaoHorario(horarioId) {
            if (!confirm('Tem certeza que deseja remover este horário? Todos os dados dos 5 dias serão perdidos.')) {
                return;
            }

            const form = document.getElementById('form-remover-horario');
            form.action = '/planejamento-semanal/horario/' + horarioId;
            form.submit();
        }

        /**
         * Submete o formulário de adição de novo horário.
         */
        function adicionarNovoHorario() {
            const inicio = document.getElementById('novo-horario-inicio').value;
            const fim = document.getElementById('novo-horario-fim').value;

            if (!inicio || !fim) {
                alert('Por favor, preencha o horário de início e fim.');
                return;
            }

            if (inicio >= fim) {
                alert('O horário de início deve ser anterior ao horário de fim.');
                return;
            }

            document.getElementById('input-novo-inicio').value = inicio;
            document.getElementById('input-novo-fim').value = fim;
            document.getElementById('form-adicionar-horario').submit();
        }
    </script>
</x-app-layout>
