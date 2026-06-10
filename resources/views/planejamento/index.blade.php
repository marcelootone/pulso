<x-app-layout>
    <x-slot name="header">
        {{ __('Planejamento Semanal') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Planejamento']
        ]" />
    </x-slot>

    <div class="max-w-full mx-auto">

        {{-- Mensagens de feedback --}}
        @if(session('success'))
            <div class="mb-6">
                <x-alert type="success" message="{{ session('success') }}" />
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6">
                <x-alert type="error" message="{{ session('error') }}" />
            </div>
        @endif

        {{-- Cabeçalho do planejamento --}}
        <x-card class="!p-0 overflow-hidden shadow-lg border-none">
            
            {{-- Título e navegação semanal --}}
            <div class="bg-gradient-to-r from-primary-700 to-primary-600 px-6 py-5">
                <h1 class="text-xl font-black text-white tracking-wide text-center uppercase mb-4 drop-shadow-sm" id="titulo-planejamento">
                    Rotina de Planejamento Pedagógico
                </h1>
                <div class="flex items-center justify-center gap-4">
                    <a href="{{ route('planejamento.index', ['data' => $semanaAnterior->toDateString()]) }}"
                       class="bg-white/10 hover:bg-white/20 text-white rounded-lg px-3 py-2 border border-white/20 transition-colors duration-200 flex items-center gap-1 backdrop-blur-sm shadow-sm"
                       id="btn-semana-anterior" title="Semana anterior">
                        <x-heroicon-o-chevron-left class="w-5 h-5" />
                        <span class="text-sm font-bold hidden sm:inline">Anterior</span>
                    </a>
                    <div class="bg-white/95 rounded-lg px-5 py-2 border border-white shadow-sm flex items-center" id="periodo-semana">
                        <x-heroicon-o-calendar class="w-5 h-5 mr-2 text-primary-600" />
                        <span class="text-primary-900 font-black text-sm uppercase tracking-wider">
                            {{ $planejamento->semana_inicio->format('d/m/Y') }}
                            <span class="text-gray-400 mx-1 text-xs">ATÉ</span>
                            {{ $planejamento->semana_fim->format('d/m/Y') }}
                        </span>
                    </div>
                    <a href="{{ route('planejamento.index', ['data' => $proximaSemana->toDateString()]) }}"
                       class="bg-white/10 hover:bg-white/20 text-white rounded-lg px-3 py-2 border border-white/20 transition-colors duration-200 flex items-center gap-1 backdrop-blur-sm shadow-sm"
                       id="btn-proxima-semana" title="Próxima semana">
                        <span class="text-sm font-bold hidden sm:inline">Próxima</span>
                        <x-heroicon-o-chevron-right class="w-5 h-5" />
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
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-2 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-10 text-center">
                                    {{-- Drag --}}
                                </th>
                                <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-36 text-center">
                                    Horário
                                </th>
                                @foreach($diasSemana as $dia)
                                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center border-l border-gray-200 bg-gray-50/50">
                                        {{ $diasLabels[$dia] }}
                                    </th>
                                @endforeach
                                <th class="px-2 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-14 text-center">
                                    {{-- Ações --}}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($planejamento->horarios as $index => $horario)
                                <tr class="bg-white hover:bg-primary-50/30 transition-colors duration-150 group" data-horario-id="{{ $horario->id }}">
                                    {{-- Coluna Drag Handle --}}
                                    <td class="px-2 py-4 align-middle text-center cursor-move drag-handle text-gray-300 hover:text-primary-600 transition-colors" title="Arraste para reordenar">
                                        <x-heroicon-o-bars-3 class="w-6 h-6 mx-auto" />
                                    </td>
                                    
                                    {{-- Coluna Horário --}}
                                    <td class="px-3 py-4 align-top">
                                        <div class="flex flex-col gap-3 bg-gray-50/50 p-2.5 rounded-lg border border-gray-100">
                                            <div>
                                                <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1 block">Início</label>
                                                <input type="time"
                                                       name="horarios[{{ $horario->id }}][horario_inicio]"
                                                       value="{{ $horario->horario_inicio }}"
                                                       class="block w-full rounded border-gray-300 text-sm py-1.5 px-2 focus:ring-primary-500 focus:border-primary-500 font-mono font-bold text-gray-700 shadow-sm"
                                                       id="horario-inicio-{{ $horario->id }}">
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1 block">Fim</label>
                                                <input type="time"
                                                       name="horarios[{{ $horario->id }}][horario_fim]"
                                                       value="{{ $horario->horario_fim }}"
                                                       class="block w-full rounded border-gray-300 text-sm py-1.5 px-2 focus:ring-primary-500 focus:border-primary-500 font-mono font-bold text-gray-700 shadow-sm"
                                                       id="horario-fim-{{ $horario->id }}">
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Colunas dos dias --}}
                                    @foreach($diasSemana as $dia)
                                        @php
                                            $item = $horario->itemDoDia($dia);
                                        @endphp
                                        <td class="px-3 py-4 align-top border-l border-gray-100">
                                            <div class="flex flex-col gap-3 bg-white p-3 rounded-xl border border-gray-100 shadow-sm group-hover:border-primary-100 transition-colors h-full">
                                                {{-- Tarefa --}}
                                                <div>
                                                    <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1 block">Tarefa</label>
                                                    <input type="text"
                                                           name="horarios[{{ $horario->id }}][itens][{{ $dia }}][tarefa]"
                                                           value="{{ $item?->tarefa }}"
                                                           placeholder="Ex: PCA, Tutoria..."
                                                           class="block w-full rounded-md border-gray-300 text-sm py-2 px-2.5 focus:ring-primary-500 focus:border-primary-500 placeholder-gray-300 font-medium shadow-sm transition-shadow hover:shadow-md focus:shadow-md"
                                                           id="tarefa-{{ $horario->id }}-{{ $dia }}">
                                                </div>

                                                {{-- Andamento --}}
                                                <div>
                                                    <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1 block">Andamento</label>
                                                    <select name="horarios[{{ $horario->id }}][itens][{{ $dia }}][andamento]"
                                                            class="block w-full rounded-md border-gray-300 text-sm py-2 px-2.5 focus:ring-primary-500 focus:border-primary-500 font-bold shadow-sm transition-all
                                                                {{ $item?->andamento === 'CONCLUIDO' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : '' }}
                                                                {{ $item?->andamento === 'EM_ANDAMENTO' ? 'bg-amber-50 text-amber-800 border-amber-200' : '' }}
                                                                {{ $item?->andamento === 'NAO_CONCLUIDO' ? 'bg-red-50 text-red-800 border-red-200' : '' }}
                                                                {{ !$item?->andamento ? 'bg-gray-50 text-gray-600' : '' }}"
                                                            onchange="atualizarCorAndamento(this)"
                                                            id="andamento-{{ $horario->id }}-{{ $dia }}">
                                                        @foreach($andamentoOptions as $value => $label)
                                                            <option value="{{ $value }}" {{ ($item?->andamento ?? '') === $value ? 'selected' : '' }} class="bg-white text-gray-800 font-medium">
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Observação --}}
                                                <div class="mt-auto">
                                                    <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1 block">Observação</label>
                                                    <textarea name="horarios[{{ $horario->id }}][itens][{{ $dia }}][observacao]"
                                                              rows="2"
                                                              placeholder="Anotações sobre a tarefa..."
                                                              class="block w-full rounded-md border-gray-300 text-xs py-2 px-2.5 focus:ring-primary-500 focus:border-primary-500 placeholder-gray-300 resize-none shadow-sm"
                                                              id="observacao-{{ $horario->id }}-{{ $dia }}">{{ $item?->observacao }}</textarea>
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach

                                    {{-- Coluna de ação: Remover --}}
                                    <td class="px-2 py-4 align-middle text-center">
                                        <button type="button"
                                                onclick="confirmarRemocaoHorario({{ $horario->id }})"
                                                class="text-red-400 hover:text-red-600 bg-white hover:bg-red-50 rounded-lg p-2 border border-transparent hover:border-red-200 transition-all duration-200 shadow-sm"
                                                title="Remover horário"
                                                id="btn-remover-{{ $horario->id }}">
                                            <x-heroicon-o-trash class="w-5 h-5" />
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($diasSemana) + 3 }}" class="px-4 py-16 text-center bg-gray-50">
                                        <x-heroicon-o-calendar class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                        <p class="text-gray-500 font-bold text-lg mb-1">Nenhum horário cadastrado</p>
                                        <p class="text-gray-400 text-sm">Adicione horários para começar a planejar sua semana.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Barra de ações fixa --}}
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    {{-- Botão Criar Horário --}}
                    <div x-data="{ open: false }" class="relative w-full sm:w-auto">
                        <button type="button"
                                @click="open = !open"
                                class="w-full sm:w-auto bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-bold py-2.5 px-5 rounded-xl shadow-sm transition-colors duration-200 flex items-center justify-center gap-2"
                                id="btn-criar-horario">
                            <x-heroicon-o-plus class="w-5 h-5 text-gray-500" />
                            Adicionar Horário
                        </button>

                        {{-- Dropdown para novo horário --}}
                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute bottom-[120%] left-0 bg-white rounded-xl shadow-xl border border-gray-200 p-5 w-72 z-50 origin-bottom-left"
                             id="dropdown-criar-horario" style="display: none;">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider flex items-center">
                                <x-heroicon-o-clock class="w-4 h-4 mr-2 text-gray-400" /> Novo Horário
                            </h3>
                            <div class="flex gap-4 mb-5">
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500 font-bold uppercase tracking-wider block mb-1.5">Início</label>
                                    <input type="time" id="novo-horario-inicio" value="08:00"
                                           class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 focus:ring-primary-500 focus:border-primary-500 font-mono shadow-sm">
                                </div>
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500 font-bold uppercase tracking-wider block mb-1.5">Fim</label>
                                    <input type="time" id="novo-horario-fim" value="08:50"
                                           class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 focus:ring-primary-500 focus:border-primary-500 font-mono shadow-sm">
                                </div>
                            </div>
                            <x-button variant="primary" type="button" onclick="adicionarNovoHorario()" class="w-full justify-center !bg-emerald-600 hover:!bg-emerald-700 h-10">
                                Confirmar
                            </x-button>
                        </div>
                    </div>

                    {{-- Botão Salvar --}}
                    <x-button variant="primary" type="submit" class="w-full sm:w-auto h-11 px-8 text-base shadow-md">
                        <x-heroicon-o-check-circle class="w-5 h-5 mr-2" /> Salvar Planejamento
                    </x-button>
                </div>
            </form>
        </x-card>

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
            if (tbody && tbody.querySelector('tr[data-horario-id]')) {
                new Sortable(tbody, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'bg-primary-50',
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

        function atualizarCorAndamento(select) {
            select.classList.remove(
                'bg-emerald-50', 'text-emerald-800', 'border-emerald-200',
                'bg-amber-50', 'text-amber-800', 'border-amber-200',
                'bg-red-50', 'text-red-800', 'border-red-200',
                'bg-gray-50', 'text-gray-600'
            );

            switch (select.value) {
                case 'CONCLUIDO':
                    select.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-200');
                    break;
                case 'EM_ANDAMENTO':
                    select.classList.add('bg-amber-50', 'text-amber-800', 'border-amber-200');
                    break;
                case 'NAO_CONCLUIDO':
                    select.classList.add('bg-red-50', 'text-red-800', 'border-red-200');
                    break;
                default:
                    select.classList.add('bg-gray-50', 'text-gray-600');
            }
        }

        function confirmarRemocaoHorario(horarioId) {
            if (!confirm('Tem certeza que deseja remover este horário? Todos os dados dos 5 dias serão perdidos.')) {
                return;
            }

            const form = document.getElementById('form-remover-horario');
            form.action = '/planejamento-semanal/horario/' + horarioId;
            form.submit();
        }

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
