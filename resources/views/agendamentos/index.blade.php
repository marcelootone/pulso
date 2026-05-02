<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reserva de Espaços') }}
            </h2>
            <a href="{{ route('espacos.index') }}" class="text-sm text-gray-700 hover:text-blue-600 font-bold bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md transition-colors border border-gray-300">
                ⚙️ Gerenciar Espaços da Escola
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach($espacos as $espaco)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg flex flex-col justify-between border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $espaco->nome }}</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                Capacidade: {{ $espaco->capacidade ? $espaco->capacidade . ' pessoas' : 'Não definida' }}
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 border-t border-gray-100">
                            <a href="{{ route('agendamentos.create', $espaco->id) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition-colors">
                                Abrir / Reservar
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($espacos->isEmpty())
                <div class="bg-white p-8 text-center rounded-lg shadow-sm">
                    <p class="text-gray-500 text-lg">Nenhum espaço cadastrado ou ativo no momento.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
