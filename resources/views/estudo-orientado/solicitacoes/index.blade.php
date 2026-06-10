<x-app-layout>
    <x-slot name="header">
        {{ __('Estudo Orientado — Minhas Solicitações') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Estudo Orientado', 'url' => '#'],
            ['label' => 'Solicitações']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">

        @if (session('success'))
            <div class="mb-6">
                <x-alert type="success" message="{{ session('success') }}" />
            </div>
        @endif

        {{-- Cabeçalho com filtros e botão criar --}}
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            {{-- Filtros --}}
            <form action="{{ route('estudo-orientado.solicitacoes.index') }}" method="GET" class="flex gap-3 flex-wrap w-full sm:w-auto">
                <x-select name="status" class="w-full sm:w-48" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="Avaliada" {{ request('status') == 'Avaliada' ? 'selected' : '' }}>Avaliada</option>
                </x-select>
                <x-select name="turma_id" class="w-full sm:w-64" onchange="this.form.submit()">
                    <option value="">Todas as Turmas</option>
                    @foreach($turmas as $turma)
                        <option value="{{ $turma->id }}" {{ request('turma_id') == $turma->id ? 'selected' : '' }}>
                            {{ $turma->serie }} {{ $turma->complemento }} ({{ $turma->turno }})
                        </option>
                    @endforeach
                </x-select>
            </form>

            {{-- Botão Nova Solicitação --}}
            @unlessrole('Secretaria')
            <x-button variant="primary" onclick="window.location='{{ route('estudo-orientado.solicitacoes.create') }}'" class="w-full sm:w-auto justify-center">
                <x-heroicon-o-plus-circle class="w-5 h-5 mr-2" />
                Nova Solicitação
            </x-button>
            @endunlessrole
        </div>

        {{-- Tabela de Atividades --}}
        <x-card>
            <div class="-mx-6 -my-6">
                <x-table>
                    <x-slot name="head">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turma</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disciplina</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Data Prevista</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($atividades as $atividade)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $atividade->turma->serie ?? '—' }} {{ $atividade->turma->complemento ?? '' }}</div>
                                    <div class="text-xs text-gray-500 mt-1 font-medium">{{ $atividade->turma->turno ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                    {{ $atividade->solicitante->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                                    {{ $atividade->disciplina_solicitante }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                    <p class="line-clamp-2" title="{{ $atividade->descricao }}">{{ $atividade->descricao }}</p>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-700 font-medium">
                                    {{ $atividade->data_prevista->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-2">
                                        @if($atividade->status === 'Pendente')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-yellow-100 text-yellow-800">
                                                <x-heroicon-o-clock class="w-4 h-4 mr-1" /> Pendente
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-green-100 text-green-800">
                                                <x-heroicon-o-check-circle class="w-4 h-4 mr-1" /> Avaliada
                                            </span>
                                            <a href="{{ route('estudo-orientado.solicitacoes.show', $atividade->id) }}" class="text-xs text-indigo-600 hover:text-indigo-900 font-bold bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition flex items-center">
                                                <x-heroicon-o-eye class="w-3 h-3 mr-1" /> Ver
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 border-dashed border-2 border-gray-200 rounded-lg">
                                    <x-heroicon-o-document-magnifying-glass class="mx-auto mb-2 w-10 h-10 text-gray-300" />
                                    <p class="text-sm font-medium text-gray-500">Nenhuma solicitação encontrada.</p>
                                    @unlessrole('Secretaria')
                                    <a href="{{ route('estudo-orientado.solicitacoes.create') }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-800 text-sm font-bold">Criar a primeira solicitação</a>
                                    @endunlessrole
                                </td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-table>
            </div>

            {{-- Paginação --}}
            @if($atividades->hasPages())
                <x-slot name="footer">
                    <div class="-mx-6 -my-4 bg-gray-50 px-6 py-4 border-t">
                        {{ $atividades->links() }}
                    </div>
                </x-slot>
            @endif
        </x-card>
    </div>
</x-app-layout>
