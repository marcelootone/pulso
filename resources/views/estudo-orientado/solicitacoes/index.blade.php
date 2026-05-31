<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Estudo Orientado — Minhas Solicitações
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Cabeçalho com filtros e botão criar --}}
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                {{-- Filtros --}}
                <form action="{{ route('estudo-orientado.solicitacoes.index') }}" method="GET" class="flex gap-3 flex-wrap">
                    <select name="status" class="border border-gray-300 text-gray-700 py-2 px-3 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                        <option value="">Todos os Status</option>
                        <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="Avaliada" {{ request('status') == 'Avaliada' ? 'selected' : '' }}>Avaliada</option>
                    </select>
                    <select name="turma_id" class="border border-gray-300 text-gray-700 py-2 px-3 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                        <option value="">Todas as Turmas</option>
                        @foreach($turmas as $turma)
                            <option value="{{ $turma->id }}" {{ request('turma_id') == $turma->id ? 'selected' : '' }}>
                                {{ $turma->serie }} {{ $turma->complemento }} ({{ $turma->turno }})
                            </option>
                        @endforeach
                    </select>
                </form>

                {{-- Botão Nova Solicitação --}}
                @unlessrole('Secretaria')
                <a href="{{ route('estudo-orientado.solicitacoes.create') }}"
                   class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-black font-semibold py-2 px-5 rounded-lg shadow transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nova Solicitação
                </a>
                @endunlessrole
            </div>

            {{-- Tabela de Atividades --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Turma</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Solicitante</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Disciplina</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Descrição</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Data Prevista</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($atividades as $atividade)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $atividade->turma->serie ?? '—' }} {{ $atividade->turma->complemento ?? '' }}
                                    <span class="block text-xs text-gray-500 font-normal">{{ $atividade->turma->turno ?? '' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $atividade->solicitante->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $atividade->disciplina_solicitante }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                    <p class="line-clamp-2">{{ $atividade->descricao }}</p>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-700">
                                    {{ $atividade->data_prevista->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-2">
                                        @if($atividade->status === 'Pendente')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                ⏳ Pendente
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✅ Avaliada
                                            </span>
                                            <a href="{{ route('estudo-orientado.solicitacoes.show', $atividade->id) }}" class="text-xs text-indigo-600 hover:text-indigo-900 font-semibold bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition">Ver Resultado</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="mx-auto mb-2 w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm">Nenhuma solicitação encontrada.</p>
                                    @unlessrole('Secretaria')
                                    <a href="{{ route('estudo-orientado.solicitacoes.create') }}" class="mt-2 inline-block text-indigo-600 hover:underline text-sm">Criar a primeira solicitação</a>
                                    @endunlessrole
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            @if($atividades->hasPages())
                <div class="mt-4">{{ $atividades->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
