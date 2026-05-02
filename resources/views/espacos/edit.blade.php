<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Espaço: {{ $espaco->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('espacos.update', $espaco->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nome do Espaço *</label>
                            <input type="text" name="nome" value="{{ old('nome', $espaco->nome) }}" required
                                   class="shadow-sm border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Capacidade (Pessoas)</label>
                            <input type="number" name="capacidade" value="{{ old('capacidade', $espaco->capacidade) }}" min="1"
                                   class="shadow-sm border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="mb-6 bg-gray-50 p-4 rounded-md border border-gray-200">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="status" value="1" {{ old('status', $espaco->status) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700">Espaço Ativo</span>
                                    <span class="text-xs text-gray-500">Desmarque para inativar. Ele não aparecerá mais para agendamentos, mas o histórico será mantido.</span>
                                </div>
                            </label>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('espacos.index') }}" class="text-gray-600 hover:text-gray-900 font-bold text-sm">Cancelar</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md shadow-sm transition-colors">
                                Atualizar Espaço
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
