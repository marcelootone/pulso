<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestão de Espaços') }}
            </h2>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <x-button variant="secondary" onclick="window.location='{{ route('agendamentos.index') }}'" class="w-full sm:w-auto justify-center">
                    <x-icon name="heroicon-o-arrow-left" class="w-5 h-5 mr-2" /> Agendamentos
                </x-button>
                <x-button variant="primary" onclick="window.location='{{ route('espacos.create') }}'" class="w-full sm:w-auto justify-center">
                    <x-icon name="heroicon-o-plus"-circle class="w-5 h-5 mr-2" /> Criar Espaço
                </x-button>
            </div>
        </div>
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Espaços']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">

        @if (session('success'))
            <div class="mb-6">
                <x-alert type="success" message="{{ session('success') }}" />
            </div>
        @endif

        <x-card class="border-t-4 border-t-primary-500">
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <x-icon name="heroicon-o-building-office-2" class="w-6 h-6 text-primary-500 mr-2" />
                    Espaços Cadastrados
                </h3>
            </x-slot>

            <div class="-mx-6 -my-6">
                <x-table>
                    <x-slot name="head">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do Espaço</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Capacidade</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($espacos as $espaco)
                        <tr class="hover:bg-gray-50 transition-colors {{ !$espaco->status ? 'bg-gray-50/50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 flex items-center">
                                    <div class="w-8 h-8 rounded-full {{ $espaco->status ? 'bg-primary-100 text-primary-600' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center mr-3 font-black text-sm">
                                        {{ substr($espaco->nome, 0, 1) }}
                                    </div>
                                    {{ $espaco->nome }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center font-medium text-gray-600">
                                @if($espaco->capacidade)
                                    <span class="bg-gray-100 text-gray-800 py-1 px-3 rounded-full text-sm border border-gray-200">
                                        {{ $espaco->capacidade }} pessoas
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($espaco->status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-green-100 text-green-800">
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-red-100 text-red-800">
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('espacos.edit', $espaco->id) }}" class="text-primary-600 hover:text-primary-900 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-md transition-colors inline-flex items-center">
                                    <x-icon name="heroicon-o-pencil-square" class="w-4 h-4 mr-1.5" /> Editar
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 border-dashed border-2 border-gray-200 rounded-lg">
                                <x-icon name="heroicon-o-building-office-2" class="mx-auto h-12 w-12 text-gray-300 mb-3" />
                                <p class="text-base font-bold text-gray-500 mb-1">Nenhum espaço cadastrado.</p>
                                <p class="text-sm font-medium">Crie o primeiro espaço para permitir agendamentos.</p>
                            </td>
                        </tr>
                        @endforelse
                    </x-slot>
                </x-table>
            </div>
        </x-card>
    </div>
</x-app-layout>
