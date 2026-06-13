<x-app-layout>
    <x-slot name="header">
        {{ __('Editar Espaço') }}: {{ $espaco->nome }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Espaços', 'url' => route('espacos.index')],
            ['label' => 'Editar Espaço']
        ]" />
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <x-card class="border-t-4 border-t-amber-500">
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <x-heroicon-o-pencil-square class="w-6 h-6 text-amber-500 mr-2" />
                        Atualizar Dados
                    </h3>
                    @if($espaco->status)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-green-100 text-green-800">
                            Ativo
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-red-100 text-red-800">
                            Inativo
                        </span>
                    @endif
                </div>
            </x-slot>
            
            @if ($errors->any())
                <div class="mb-6">
                    <x-alert type="error">
                        <ul class="list-disc list-inside font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                </div>
            @endif

            <form id="form-f19850" action="{{ route('espacos.update', $espaco->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Nome do Espaço <span class="text-red-500">*</span></label>
                        <x-input type="text" name="nome" value="{{ old('nome', $espaco->nome) }}" required class="w-full" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Capacidade (Pessoas)</label>
                        <x-input type="number" name="capacidade" value="{{ old('capacidade', $espaco->capacidade) }}" min="1" class="w-full sm:w-48" />
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <label class="flex items-start cursor-pointer group">
                            <div class="relative flex items-center pt-0.5">
                                <input type="checkbox" name="status" value="1" {{ old('status', $espaco->status) ? 'checked' : '' }}
                                       class="peer shrink-0 appearance-none w-6 h-6 border-2 border-gray-300 rounded-md bg-white checked:bg-amber-500 checked:border-0 focus:outline-none focus:ring-offset-0 focus:ring-2 focus:ring-amber-200 transition-all cursor-pointer">
                                <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none hidden peer-checked:block text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div class="ml-3 flex flex-col">
                                <span class="text-sm font-bold text-gray-900 group-hover:text-amber-700 transition-colors">Espaço Ativo</span>
                                <span class="text-xs font-medium text-gray-500 mt-1 leading-relaxed max-w-lg">
                                    Desmarque para inativar. O espaço não aparecerá mais como opção para novos agendamentos, mas o histórico de reservas já feitas será mantido.
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex items-center justify-end gap-3">
                        <x-button variant="secondary" type="button" onclick="window.location='{{ route('espacos.index') }}'">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit" form="form-f19850" class="!bg-amber-500 hover:!bg-amber-600 border-none">
                            <x-heroicon-o-check class="w-5 h-5 mr-2" /> Atualizar Espaço
                        </x-button>
                    </div>
                </x-slot>
            </form>
        </x-card>
    </div>
</x-app-layout>
