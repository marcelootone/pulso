<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Avaliar Atividade de Estudo Orientado
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Card de informações da atividade --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-semibold text-lg">
                                {{ $atividade->turma->serie ?? '—' }} {{ $atividade->turma->complemento ?? '' }}
                                <span class="font-normal text-indigo-200">— {{ $atividade->turma->turno ?? '' }}</span>
                            </h3>
                            <p class="text-indigo-200 text-sm mt-0.5">Data prevista: {{ $atividade->data_prevista->format('d/m/Y') }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $atividade->status === 'Pendente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                            {{ $atividade->status === 'Pendente' ? '⏳ Pendente' : '✅ Avaliada' }}
                        </span>
                    </div>
                </div>
                <div class="px-8 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Professor Solicitante</p>
                            <p class="text-sm font-medium text-gray-800">{{ $atividade->solicitante->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Disciplina</p>
                            <p class="text-sm font-medium text-indigo-600">{{ $atividade->disciplina_solicitante }}</p>
                        </div>
                        @if(isset($somenteLeitura) && $somenteLeitura)
                            @php
                                $professoresEO = $atividade->turma->professores->where('tipo_usuario', \App\Models\User::TIPO_PROF_ESTUDO_ORIENTADO)->pluck('name')->join(', ');
                            @endphp
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Avaliador (Estudo Orientado)</p>
                                <p class="text-sm font-medium text-green-700">{{ $professoresEO ?: 'Não atribuído' }}</p>
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Descrição da Atividade</p>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $atividade->descricao }}</p>
                    </div>
                </div>
            </div>

            {{-- Formulário de checklist de alunos --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mt-8">
                <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">
                            Registro de Cumprimento
                        </h4>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $alunos->count() }} alunos matriculados nesta turma
                        </p>
                    </div>
                    @if($atividade->status === 'Avaliada')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Avaliação já registrada. Ajuste e salve novamente se necessário.
                        </span>
                    @endif
                </div>

                <form action="{{ isset($somenteLeitura) && $somenteLeitura ? '#' : route('estudo-orientado.avaliacoes.store', $atividade->id) }}" method="POST">
                    @csrf

                    {{-- Botões de marcar/desmarcar todos --}}
                    @if(!isset($somenteLeitura) || !$somenteLeitura)
                    <div class="px-8 py-4 bg-white border-b border-gray-100 flex items-center gap-6">
                        <button type="button" id="btn-marcar-todos"
                            class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Marcar Todos
                        </button>
                        <div class="w-px h-6 bg-gray-200"></div>
                        <button type="button" id="btn-desmarcar-todos"
                            class="flex items-center gap-2 text-sm text-red-500 hover:text-red-700 font-semibold transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Desmarcar Todos
                        </button>
                    </div>
                    @endif

                    <div class="divide-y divide-gray-100">
                        @forelse($alunos as $aluno)
                            @php
                                $cumprimento = $cumprimentosExistentes[$aluno->id] ?? null;
                            @endphp
                            <div class="px-8 py-5 flex items-start gap-6 hover:bg-indigo-50/50 transition-colors group">

                                {{-- Checkbox de cumprimento customizado --}}
                                <div class="flex items-center pt-1">
                                    <div class="relative flex items-center">
                                        <input type="checkbox"
                                            id="cumpriu_{{ $aluno->id }}"
                                            name="cumprimentos[{{ $aluno->id }}][cumpriu]"
                                            value="1"
                                            class="aluno-checkbox peer shrink-0 appearance-none w-6 h-6 border-2 border-gray-300 rounded-md bg-white mt-1 checked:bg-indigo-600 checked:border-0 focus:outline-none focus:ring-offset-0 focus:ring-2 focus:ring-indigo-100 disabled:border-gray-200 disabled:bg-gray-100 transition-all {{ (!isset($somenteLeitura) || !$somenteLeitura) ? 'cursor-pointer' : 'cursor-not-allowed' }}"
                                            {{ ($cumprimento && $cumprimento->cumpriu) ? 'checked' : '' }}
                                            {{ (isset($somenteLeitura) && $somenteLeitura) ? 'disabled' : '' }}>
                                        <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none hidden peer-checked:block text-white mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Nome e observação --}}
                                <div class="flex-1">
                                    <label for="cumpriu_{{ $aluno->id }}" class="block font-semibold text-gray-800 text-base cursor-pointer mb-1 group-hover:text-indigo-900 transition-colors">
                                        {{ $aluno->nome }}
                                        <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">RA: {{ $aluno->ra }}</span>
                                    </label>
                                    <input type="text"
                                        name="cumprimentos[{{ $aluno->id }}][observacao]"
                                        value="{{ old('cumprimentos.'.$aluno->id.'.observacao', $cumprimento?->observacao) }}"
                                        placeholder="{{ (isset($somenteLeitura) && $somenteLeitura) ? 'Sem observação' : 'Adicionar uma observação sobre o aluno (opcional)...' }}"
                                        maxlength="500"
                                        class="w-full mt-2 border-gray-300 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 py-2 px-3 shadow-sm transition-shadow {{ (isset($somenteLeitura) && $somenteLeitura) ? 'bg-gray-50 cursor-not-allowed' : '' }}"
                                        {{ (isset($somenteLeitura) && $somenteLeitura) ? 'readonly' : '' }}>
                                </div>

                                {{-- Indicador visual de status anterior --}}
                                @if($cumprimento)
                                    <div class="shrink-0 pt-2 flex items-center justify-center w-10">
                                        @if($cumprimento->cumpriu)
                                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600" title="Registrado como: Cumpriu">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-500" title="Registrado como: Não cumpriu">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="px-8 py-16 text-center text-gray-400">
                                <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <p class="text-base font-medium text-gray-500">Nenhum aluno ativo encontrado nesta turma.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($alunos->count() > 0)
                        <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex justify-between items-center rounded-b-2xl">
                            <a href="{{ (isset($somenteLeitura) && $somenteLeitura) ? route('estudo-orientado.solicitacoes.index') : route('estudo-orientado.avaliacoes.index') }}"
                               class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Voltar
                            </a>
                            @if(!isset($somenteLeitura) || !$somenteLeitura)
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500/50 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                                Salvar Avaliação
                            </button>
                            @endif
                        </div>
                    @endif
                </form>
            </div>

        </div>
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
