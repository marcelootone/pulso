<x-app-layout>
    <x-slot name="header">
        {{ __('Monitoramento Escolar (Coordenador)') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Pedagógico', 'url' => '#'],
            ['label' => 'Frequência']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">
        {{-- Navegação Secundária do Módulo --}}
        <div class="mb-6 flex flex-wrap gap-3">
            <x-button variant="primary" onclick="window.location='{{ route('frequencia.index') }}'">
                Visão Geral
            </x-button>
            <x-button variant="secondary" onclick="window.location='{{ route('frequencia.monitorar') }}'">
                Lançar Chamada
            </x-button>
            <x-button variant="danger" onclick="window.location='{{ route('frequencia.busca_ativa') }}'">
                Busca Ativa (Faltas)
            </x-button>
        </div>

        {{-- Filtro de Mês/Ano --}}
        <x-card class="mb-8 border-l-4 border-l-primary-500">
            <form method="GET" action="{{ route('frequencia.index') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Mês</label>
                    <x-select name="mes" class="w-32">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </x-select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Ano</label>
                    <x-select name="ano" class="w-32">
                        @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                            <option value="{{ $i }}" {{ $ano == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </x-select>
                </div>
                <div class="pb-0">
                    <x-button variant="primary" type="submit">
                        <x-heroicon-o-funnel class="w-5 h-5 mr-2" />
                        Filtrar
                    </x-button>
                </div>
            </form>
        </x-card>

        {{-- Grid de Turmas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($turmas as $turma)
                <x-card class="border-t-4 {{ $turma->percentual_frequencia < 75 ? 'border-t-red-500' : 'border-t-green-500' }} flex flex-col h-full">
                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $turma->serie }}º {{ $turma->complemento }}</h3>
                                <p class="text-sm text-gray-500 font-medium">{{ $turma->modalidade }} &bull; {{ $turma->turno }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-black {{ $turma->percentual_frequencia < 75 ? 'text-red-500' : 'text-green-500' }}">
                                    {{ number_format($turma->percentual_frequencia, 1) }}%
                                </span>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6 flex items-center justify-between">
                            <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Dias Letivos</span>
                            <span class="text-lg font-bold text-primary-700">{{ $turma->dias_letivos_registrados }} <span class="text-sm text-gray-500 font-medium">dias no mês</span></span>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="-mx-6 -my-4 bg-gray-50 px-6 py-4 border-t">
                            <x-button variant="secondary" class="w-full justify-center" onclick="window.location='{{ route('frequencia.monitorar', ['turma_id' => $turma->id]) }}'">
                                Acessar Chamada
                            </x-button>
                        </div>
                    </x-slot>
                </x-card>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                    <x-heroicon-o-clipboard-document-list class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma turma encontrada</h3>
                    <p class="mt-1 text-sm text-gray-500">Nenhuma turma ativa encontrada para monitoramento neste período.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
