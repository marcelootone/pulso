<x-app-layout>
    <x-slot name="header">
        {{ __('Meu Diário de Classe') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Pedagógico', 'url' => '#'],
            ['label' => 'Diário']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900">Selecione uma Turma</h3>
            <p class="text-sm text-gray-500">Escolha a turma para realizar a chamada e registrar o conteúdo lecionado.</p>
        </div>

        @if($minhasTurmas->isEmpty())
            <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-yellow-200">
                <x-icon name="heroicon-o-exclamation-triangle" class="mx-auto h-12 w-12 text-yellow-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma turma atribuída</h3>
                <p class="mt-1 text-sm text-gray-500">Você ainda não foi atribuído a nenhuma turma. Procure a Secretaria ou Coordenação.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($minhasTurmas as $turma)
                    <x-card class="hover:shadow-md transition-shadow border-t-4 border-t-blue-500 flex flex-col h-full">
                        <div class="flex-grow">
                            <h4 class="text-xl font-bold text-gray-900 uppercase mb-1">
                                {{ $turma->serie }}º {{ $turma->complemento }}
                            </h4>
                            <p class="text-sm text-gray-500 mb-4">{{ $turma->modalidade }} &bull; {{ $turma->turno }}</p>

                            <div class="bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-md inline-flex items-center mb-6">
                                <x-icon name="heroicon-o-book-open" class="w-4 h-4 mr-1.5" />
                                Disciplina: {{ $turma->pivot->disciplina }}
                            </div>
                        </div>

                        <x-slot name="footer">
                            <div class="-mx-6 -my-4 bg-gray-50 px-6 py-4 border-t">
                                <x-button variant="primary" class="w-full justify-center" onclick="window.location='{{ route('diario.show', $turma->id) }}'">
                                    Fazer Chamada
                                </x-button>
                            </div>
                        </x-slot>
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
