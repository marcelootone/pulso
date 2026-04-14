<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meu Diário de Classe') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <h3 class="text-lg font-bold text-gray-700 mb-4">Selecione uma Turma para realizar a chamada:</h3>

            @if($minhasTurmas->isEmpty())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 shadow-sm">
                    <p class="text-sm text-yellow-700 font-bold">Você ainda não foi atribuído a nenhuma turma. Procure a Secretaria.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($minhasTurmas as $turma)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-500 hover:shadow-lg transition-shadow">
                            <div class="p-6">
                                <h4 class="text-xl font-bold text-gray-800 uppercase mb-1">
                                    {{ $turma->serie }}º {{ $turma->complemento }}
                                </h4>
                                <p class="text-sm text-gray-500 mb-4">{{ $turma->modalidade }} - {{ $turma->turno }}</p>
                                
                                <div class="bg-blue-50 text-blue-800 text-xs font-bold px-2 py-1 rounded inline-block mb-4">
                                    Disciplina: {{ $turma->pivot->disciplina }}
                                </div>

                                <a href="{{ route('diario.show', $turma->id) }}" class="block w-full text-center bg-blue-600 text-black px-4 py-2 rounded-md hover:bg-blue-700 font-bold">
                                    FAZER CHAMADA
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>