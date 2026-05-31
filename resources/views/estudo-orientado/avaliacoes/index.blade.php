<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Estudo Orientado — Atividades para Avaliar
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Filtro de status --}}
            <div class="mb-6 flex justify-between items-center">
                <form action="{{ route('estudo-orientado.avaliacoes.index') }}" method="GET" class="flex gap-3">
                    <select name="status" class="border border-gray-300 text-gray-700 py-2 px-3 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                        <option value="">Todas (Pendentes + Avaliadas)</option>
                        <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>⏳ Somente Pendentes</option>
                        <option value="Avaliada" {{ request('status') == 'Avaliada' ? 'selected' : '' }}>✅ Somente Avaliadas</option>
                    </select>
                </form>
            </div>

            {{-- Cards de Atividades --}}
            @forelse($atividades as $atividade)
                <div class="bg-white rounded-xl shadow-sm border {{ $atividade->status === 'Pendente' ? 'border-yellow-300' : 'border-green-200' }} overflow-hidden mb-4">
                    <div class="flex items-start justify-between p-5 gap-4">

                        {{-- Lado esquerdo: informações --}}
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                {{-- Badge de Status --}}
                                @if($atividade->status === 'Pendente')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⏳ Pendente</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Avaliada</span>
                                @endif
                                <span class="text-xs text-gray-400">Data prevista: {{ $atividade->data_prevista->format('d/m/Y') }}</span>
                            </div>

                            <h3 class="text-base font-semibold text-gray-800 mb-0.5">
                                {{ $atividade->turma->serie ?? '—' }} {{ $atividade->turma->complemento ?? '' }}
                                <span class="font-normal text-gray-500 text-sm">— {{ $atividade->turma->turno ?? '' }}</span>
                            </h3>
                            <p class="text-sm text-gray-500 mb-2">
                                Solicitado por: <span class="font-medium text-gray-700">{{ $atividade->solicitante->name ?? '—' }}</span>
                                | Disciplina: <span class="font-medium text-indigo-600">{{ $atividade->disciplina_solicitante }}</span>
                            </p>
                            <p class="text-sm text-gray-700 leading-relaxed line-clamp-3">{{ $atividade->descricao }}</p>

                            @if($atividade->status === 'Avaliada')
                                <p class="mt-2 text-xs text-gray-400">
                                    {{ $atividade->cumprimentos->where('cumpriu', true)->count() }} de {{ $atividade->cumprimentos->count() }} alunos cumpriram.
                                </p>
                            @endif
                        </div>

                        {{-- Lado direito: botão de ação --}}
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <a href="{{ route('estudo-orientado.avaliacoes.show', $atividade->id) }}"
                               class="{{ $atividade->status === 'Pendente' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-500 hover:bg-gray-600' }} text-black text-sm font-semibold py-2 px-4 rounded-lg shadow transition whitespace-nowrap">
                                {{ $atividade->status === 'Pendente' ? '▶ Avaliar Alunos' : '🔍 Ver Resultado' }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <svg class="mx-auto mb-3 w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500 font-medium text-sm">Nenhuma atividade encontrada.</p>
                    @hasrole('Professor de Estudo Orientado')
                        <p class="text-gray-400 text-xs mt-2 max-w-sm mx-auto">
                            Você ainda não está vinculado a nenhuma turma, ou nenhum professor registrou uma atividade para as suas turmas.
                            Solicite ao Gestor ou Secretaria que faça sua atribuição em
                            <strong>Atribuir Aulas</strong>.
                        </p>
                    @endhasrole
                    @hasrole('Gestor|Coordenador')
                        <p class="text-gray-400 text-xs mt-2">Nenhum professor cadastrou solicitações de Estudo Orientado ainda.</p>
                    @endhasrole
                </div>
            @endforelse

            @if($atividades->hasPages())
                <div class="mt-4">{{ $atividades->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
