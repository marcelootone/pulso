<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Análise de Encaminhamentos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <form method="GET" action="{{ route('estudo-orientado.analises.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="w-full md:w-1/3">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Todos</option>
                                <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="Aprovada" {{ request('status') == 'Aprovada' ? 'selected' : '' }}>Aprovada</option>
                                <option value="Rejeitada" {{ request('status') == 'Rejeitada' ? 'selected' : '' }}>Rejeitada</option>
                                <option value="EmAtendimento" {{ request('status') == 'EmAtendimento' ? 'selected' : '' }}>Em Atendimento</option>
                                <option value="Concluida" {{ request('status') == 'Concluida' ? 'selected' : '' }}>Concluída</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition ease-in-out duration-150">
                                {{ __('Filtrar') }}
                            </button>
                            <a href="{{ route('estudo-orientado.analises.index') }}" class="inline-flex items-center justify-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-md shadow-sm transition ease-in-out duration-150">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($solicitacoes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aluno</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($solicitacoes as $solicitacao)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $solicitacao->aluno->nome }}</div>
                                                <div class="text-xs text-gray-500">{{ $solicitacao->turma->serie }} {{ $solicitacao->turma->complemento ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $solicitacao->solicitante->name }}<br>
                                                <span class="text-xs text-gray-400">{{ $solicitacao->created_at->format('d/m/Y') }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($solicitacao->prioridade == 'Alta')
                                                    <span class="px-2 text-xs font-semibold rounded-full bg-red-100 text-red-800">Alta</span>
                                                @elseif($solicitacao->prioridade == 'Media')
                                                    <span class="px-2 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Média</span>
                                                @else
                                                    <span class="px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">Baixa</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $solicitacao->status === 'Pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $solicitacao->status === 'Aprovada' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $solicitacao->status === 'Rejeitada' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $solicitacao->status === 'EmAtendimento' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                                    {{ $solicitacao->status === 'Concluida' ? 'bg-green-100 text-green-800' : '' }}
                                                ">
                                                    {{ $solicitacao->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('estudo-orientado.analises.show', $solicitacao->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Analisar</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $solicitacoes->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            Nenhuma solicitação encontrada.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
