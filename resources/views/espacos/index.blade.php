<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestão de Espaços
            </h2>
            <div class="space-x-4">
                <a href="{{ route('agendamentos.index') }}" class="text-sm text-gray-600 hover:underline font-bold">⬅ Voltar aos Agendamentos</a>
                <a href="{{ route('espacos.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-bold text-sm transition-colors shadow-sm">
                    + Criar Novo Espaço
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-6" role="alert">
                    <p class="font-bold">Sucesso</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                                <th class="p-3">Nome do Espaço</th>
                                <th class="p-3 text-center">Capacidade</th>
                                <th class="p-3 text-center">Status</th>
                                <th class="p-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($espacos as $espaco)
                            <tr class="border-b hover:bg-gray-50 transition-colors">
                                <td class="p-3 font-bold text-gray-800">{{ $espaco->nome }}</td>
                                <td class="p-3 text-center text-gray-600">{{ $espaco->capacidade ?? '-' }}</td>
                                <td class="p-3 text-center">
                                    @if($espaco->status)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Ativo</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">Inativo</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('espacos.edit', $espaco->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-sm underline">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-gray-500">
                                    Nenhum espaço cadastrado no sistema.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
