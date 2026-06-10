<x-app-layout>
    <x-slot name="header">
        {{ __('Avaliar Atividade de Estudo Orientado') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Estudo Orientado', 'url' => isset($somenteLeitura) && $somenteLeitura ? route('estudo-orientado.solicitacoes.index') : route('estudo-orientado.avaliacoes.index')],
            ['label' => isset($somenteLeitura) && $somenteLeitura ? 'Visualizar Resultado' : 'Avaliar']
        ]" />
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Card de informações da atividade --}}
        <x-card class="border-t-4 border-t-indigo-600 overflow-hidden !p-0">
            <div class="bg-indigo-50 px-6 py-5 border-b border-indigo-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-indigo-900 font-black text-xl flex items-center">
                        {{ $atividade->turma->serie ?? '—' }} {{ $atividade->turma->complemento ?? '' }}
                        <span class="font-bold text-indigo-600 text-sm ml-2 bg-white px-2 py-0.5 rounded border border-indigo-200 shadow-sm">{{ $atividade->turma->turno ?? '' }}</span>
                    </h3>
                    <p class="text-indigo-700 font-medium text-sm mt-1 flex items-center">
                        <x-heroicon-o-calendar class="w-4 h-4 mr-1" />
                        Data prevista: {{ $atividade->data_prevista->format('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold uppercase tracking-wider shadow-sm {{ $atividade->status === 'Pendente' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                        @if($atividade->status === 'Pendente')
                            <x-heroicon-o-clock class="w-4 h-4 mr-1.5" /> Pendente
                        @else
                            <x-heroicon-o-check-circle class="w-4 h-4 mr-1.5" /> Avaliada
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 flex items-center"><x-heroicon-o-user class="w-4 h-4 mr-1" /> Professor Solicitante</p>
                        <p class="text-base font-black text-gray-900">{{ $atividade->solicitante->name ?? '—' }}</p>
                    </div>
                    <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                        <p class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-1 flex items-center"><x-heroicon-o-book-open class="w-4 h-4 mr-1" /> Disciplina</p>
                        <p class="text-base font-black text-indigo-700">{{ $atividade->disciplina_solicitante }}</p>
                    </div>
                    @if(isset($somenteLeitura) && $somenteLeitura)
                        @php
                            $professoresEO = $atividade->turma->professores->where('tipo_usuario', \App\Models\User::TIPO_PROF_ESTUDO_ORIENTADO)->pluck('name')->join(', ');
                        @endphp
                        <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                            <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-1 flex items-center"><x-heroicon-o-check-badge class="w-4 h-4 mr-1" /> Avaliador (Estudo Orientado)</p>
                            <p class="text-base font-black text-green-800">{{ $professoresEO ?: 'Não atribuído' }}</p>
                        </div>
                    @endif
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center"><x-heroicon-o-document-text class="w-4 h-4 mr-1" /> Descrição da Atividade</p>
                    <p class="text-sm font-medium text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $atividade->descricao }}</p>
                </div>
            </div>
        </x-card>

        {{-- Formulário de checklist de alunos --}}
        <x-card class="!p-0 overflow-hidden shadow-md">
            <div class="px-6 py-5 border-b border-gray-200 flex flex-col md:flex-row items-center justify-between bg-gray-50/80 gap-4">
                <div class="flex items-center">
                    <div class="bg-indigo-100 p-2.5 rounded-lg text-indigo-600 mr-4 shadow-sm border border-indigo-200">
                        <x-heroicon-o-clipboard-document-check class="w-6 h-6" />
                    </div>
                    <div>
                        <h4 class="font-black text-gray-900 text-lg">
                            Registro de Cumprimento
                        </h4>
                        <p class="text-sm font-medium text-gray-500 mt-0.5">
                            <span class="font-bold text-gray-700">{{ $alunos->count() }}</span> alunos matriculados nesta turma
                        </p>
                    </div>
                </div>
                
                @if($atividade->status === 'Avaliada')
                    <div class="bg-blue-50 text-blue-800 border border-blue-200 px-4 py-2.5 rounded-lg text-sm font-bold flex items-center shadow-sm">
                        <x-heroicon-o-information-circle class="w-5 h-5 mr-2 text-blue-600" />
                        Avaliação já registrada. Ajuste e salve novamente se necessário.
                    </div>
                @endif
            </div>

            <form action="{{ isset($somenteLeitura) && $somenteLeitura ? '#' : route('estudo-orientado.avaliacoes.store', $atividade->id) }}" method="POST">
                @csrf

                {{-- Botões de marcar/desmarcar todos --}}
                @if(!isset($somenteLeitura) || !$somenteLeitura)
                <div class="px-6 py-4 bg-white border-b border-gray-100 flex items-center gap-4 bg-gray-50/30">
                    <button type="button" id="btn-marcar-todos"
                        class="flex items-center gap-1.5 text-sm bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 px-3 py-1.5 rounded-md font-bold transition-colors">
                        <x-heroicon-o-check-circle class="w-4 h-4" /> Marcar Todos
                    </button>
                    <button type="button" id="btn-desmarcar-todos"
                        class="flex items-center gap-1.5 text-sm bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 px-3 py-1.5 rounded-md font-bold transition-colors">
                        <x-heroicon-o-x-mark class="w-4 h-4" /> Desmarcar Todos
                    </button>
                </div>
                @endif

                <div class="divide-y divide-gray-100 bg-white">
                    @forelse($alunos as $aluno)
                        @php
                            $cumprimento = $cumprimentosExistentes[$aluno->id] ?? null;
                        @endphp
                        <div class="px-6 py-5 flex items-start gap-5 hover:bg-indigo-50/40 transition-colors group">

                            {{-- Checkbox de cumprimento customizado --}}
                            <div class="flex items-center pt-1.5">
                                <div class="relative flex items-center">
                                    <input type="checkbox"
                                        id="cumpriu_{{ $aluno->id }}"
                                        name="cumprimentos[{{ $aluno->id }}][cumpriu]"
                                        value="1"
                                        class="aluno-checkbox peer shrink-0 appearance-none w-7 h-7 border-2 border-gray-300 rounded-md bg-white checked:bg-indigo-600 checked:border-0 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-indigo-500 disabled:border-gray-200 disabled:bg-gray-100 transition-all shadow-sm {{ (!isset($somenteLeitura) || !$somenteLeitura) ? 'cursor-pointer' : 'cursor-not-allowed' }}"
                                        {{ ($cumprimento && $cumprimento->cumpriu) ? 'checked' : '' }}
                                        {{ (isset($somenteLeitura) && $somenteLeitura) ? 'disabled' : '' }}>
                                    <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4.5 h-4.5 pointer-events-none hidden peer-checked:block text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                            </div>

                            {{-- Nome e observação --}}
                            <div class="flex-1">
                                <label for="cumpriu_{{ $aluno->id }}" class="block font-black text-gray-900 text-base cursor-pointer mb-1.5 group-hover:text-indigo-700 transition-colors">
                                    {{ $aluno->nome }}
                                    <span class="inline-flex items-center ml-2 px-2.5 py-0.5 rounded-md text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200 font-mono tracking-tight">RA: {{ $aluno->ra }}</span>
                                </label>
                                <input type="text"
                                    name="cumprimentos[{{ $aluno->id }}][observacao]"
                                    value="{{ old('cumprimentos.'.$aluno->id.'.observacao', $cumprimento?->observacao) }}"
                                    placeholder="{{ (isset($somenteLeitura) && $somenteLeitura) ? 'Sem observação' : 'Adicionar uma observação sobre o aluno (opcional)...' }}"
                                    maxlength="500"
                                    class="w-full border-gray-300 rounded-lg text-sm font-medium text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-3 shadow-sm transition-all {{ (isset($somenteLeitura) && $somenteLeitura) ? 'bg-gray-50 cursor-not-allowed text-gray-500' : 'bg-white hover:border-indigo-300' }}"
                                    {{ (isset($somenteLeitura) && $somenteLeitura) ? 'readonly' : '' }}>
                            </div>

                            {{-- Indicador visual de status anterior --}}
                            @if($cumprimento)
                                <div class="shrink-0 pt-2 flex items-center justify-center w-12">
                                    @if($cumprimento->cumpriu)
                                        <div class="w-10 h-10 rounded-full bg-green-100 border border-green-200 flex items-center justify-center text-green-600 shadow-sm" title="Registrado como: Cumpriu">
                                            <x-heroicon-o-check class="w-6 h-6" />
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-red-100 border border-red-200 flex items-center justify-center text-red-500 shadow-sm" title="Registrado como: Não cumpriu">
                                            <x-heroicon-o-x-mark class="w-6 h-6" />
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center text-gray-400 bg-gray-50">
                            <x-heroicon-o-users class="mx-auto w-14 h-14 text-gray-300 mb-4" />
                            <p class="text-lg font-bold text-gray-500">Nenhum aluno ativo encontrado nesta turma.</p>
                        </div>
                    @endforelse
                </div>

                @if($alunos->count() > 0)
                    <div class="px-6 py-5 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                        <x-button variant="secondary" type="button" onclick="window.location='{{ (isset($somenteLeitura) && $somenteLeitura) ? route('estudo-orientado.solicitacoes.index') : route('estudo-orientado.avaliacoes.index') }}'">
                            <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" /> Voltar
                        </x-button>
                        
                        @if(!isset($somenteLeitura) || !$somenteLeitura)
                        <x-button variant="primary" type="submit" class="!bg-indigo-600 hover:!bg-indigo-700 h-11 px-6 text-base shadow-md">
                            <x-heroicon-o-check-circle class="w-5 h-5 mr-2" /> Salvar Avaliação
                        </x-button>
                        @endif
                    </div>
                @endif
            </form>
        </x-card>
    </div>

    {{-- Script inline (sem o push, para garantir renderização) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnMarcarTodos = document.getElementById('btn-marcar-todos');
            const btnDesmarcarTodos = document.getElementById('btn-desmarcar-todos');
            
            if(btnMarcarTodos && btnDesmarcarTodos) {
                btnMarcarTodos.addEventListener('click', function () {
                    document.querySelectorAll('.aluno-checkbox').forEach(cb => cb.checked = true);
                });
                btnDesmarcarTodos.addEventListener('click', function () {
                    document.querySelectorAll('.aluno-checkbox').forEach(cb => cb.checked = false);
                });
            }
        });
    </script>
</x-app-layout>
