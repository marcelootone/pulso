<x-app-layout>
    <x-slot name="header">
        {{ __('Criar Novo Espaço') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Espaços', 'url' => route('espacos.index')],
            ['label' => 'Novo Espaço']
        ]" />
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <x-card class="border-t-4 border-t-primary-500">
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <x-heroicon-o-plus-circle class="w-6 h-6 text-primary-500 mr-2" />
                    Adicionar Espaço
                </h3>
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

            <form id="form-1475a1" action="{{ route('espacos.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Nome do Espaço <span class="text-red-500">*</span></label>
                        <x-input type="text" name="nome" value="{{ old('nome') }}" required placeholder="Ex: Sala de Leitura, Laboratório de Informática..." class="w-full" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Capacidade (Pessoas)</label>
                        <x-input type="number" name="capacidade" value="{{ old('capacidade') }}" min="1" placeholder="Ex: 40" class="w-full sm:w-48" />
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}
                                       class="peer shrink-0 appearance-none w-6 h-6 border-2 border-gray-300 rounded-md bg-white mt-1 checked:bg-primary-600 checked:border-0 focus:outline-none focus:ring-offset-0 focus:ring-2 focus:ring-primary-100 transition-all cursor-pointer">
                                <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none hidden peer-checked:block text-white mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div class="ml-3 flex flex-col">
                                <span class="text-sm font-bold text-gray-900 group-hover:text-primary-700 transition-colors">Espaço Ativo</span>
                                <span class="text-xs font-medium text-gray-500 mt-0.5">Permitir que este espaço seja reservado em agendamentos.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex items-center justify-end gap-3">
                        <x-button variant="secondary" type="button" onclick="window.location='{{ route('espacos.index') }}'">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit" form="form-1475a1">
                            <x-heroicon-o-check class="w-5 h-5 mr-2" /> Salvar Espaço
                        </x-button>
                    </div>
                </x-slot>
            </form>
        </x-card>
    </div>
</x-app-layout>
