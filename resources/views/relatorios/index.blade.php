<x-app-layout>
    <x-slot name="header">
        {{ __('Central de Relatórios') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Relatórios']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            {{-- Relatório 1: Alerta de Evasão --}}
            <x-card class="h-full flex flex-col justify-between border-t-4 border-t-red-500 hover:shadow-lg transition-shadow">
                <div class="flex-grow">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-red-50 rounded-xl text-red-600 mr-4 border border-red-100 shadow-sm">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                        </div>
                        <h3 class="text-lg font-black text-gray-900">Alerta de Evasão</h3>
                    </div>
                    <p class="text-sm text-gray-600 font-medium leading-relaxed mb-6">Gera um relatório completo com todos os alunos da escola que estão com a frequência global abaixo de 75%.</p>
                </div>
                <div class="mt-auto pt-6 border-t border-gray-100">
                    <x-button variant="primary" onclick="window.location='{{ route('relatorios.evasao') }}'" class="w-full justify-center !bg-red-600 hover:!bg-red-700 shadow-md">
                        <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2" /> BAIXAR PDF
                    </x-button>
                </div>
            </x-card>

            {{-- Relatório 2: Frequência Mensal --}}
            <x-card class="h-full flex flex-col justify-between border-t-4 border-t-primary-500 hover:shadow-lg transition-shadow">
                <div class="flex-grow">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-primary-50 rounded-xl text-primary-600 mr-4 border border-primary-100 shadow-sm">
                            <x-heroicon-o-calendar-days class="w-6 h-6" />
                        </div>
                        <h3 class="text-lg font-black text-gray-900">Frequência da Turma</h3>
                    </div>
                    <p class="text-sm text-gray-600 font-medium leading-relaxed mb-6">Mapa detalhado dia-a-dia de presença, falta e falta justificada de todos os alunos de uma turma em um mês específico.</p>
                    
                    <form action="{{ route('relatorios.frequencia_mensal') }}" method="GET" class="space-y-4 flex flex-col h-full">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Turma</label>
                            <x-select name="turma_id" required class="w-full">
                                <option value="">Selecione...</option>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}">{{ $turma->serie }}º {{ $turma->complemento }} ({{ $turma->turno }})</option>
                                @endforeach
                            </x-select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Mês</label>
                                <x-select name="mes" required class="w-full">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </x-select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Ano</label>
                                <x-select name="ano" required class="w-full">
                                    @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </x-select>
                            </div>
                        </div>
                        <div class="pt-6 border-t border-gray-100 mt-auto">
                            <x-button variant="primary" type="submit" class="w-full justify-center shadow-md">
                                <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2" /> GERAR PDF (PAISAGEM)
                            </x-button>
                        </div>
                    </form>
                </div>
            </x-card>

            {{-- Relatório 3: Ranking de Faltas --}}
            <x-card class="h-full flex flex-col justify-between border-t-4 border-t-orange-500 hover:shadow-lg transition-shadow">
                <div class="flex-grow">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-orange-50 rounded-xl text-orange-600 mr-4 border border-orange-100 shadow-sm">
                            <x-heroicon-o-chart-bar class="w-6 h-6" />
                        </div>
                        <h3 class="text-lg font-black text-gray-900">Ranking de Faltas</h3>
                    </div>
                    <p class="text-sm text-gray-600 font-medium leading-relaxed mb-6">Lista as turmas ordenadas pelo índice de ausência (turmas que mais faltam aparecem primeiro) em determinado mês.</p>
                    
                    <form action="{{ route('relatorios.turmas_faltas') }}" method="GET" class="space-y-4 flex flex-col h-full">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Mês</label>
                                <x-select name="mes" required class="w-full">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </x-select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Ano</label>
                                <x-select name="ano" required class="w-full">
                                    @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </x-select>
                            </div>
                        </div>
                        <div class="pt-6 border-t border-gray-100 mt-auto">
                            <x-button variant="primary" type="submit" class="w-full justify-center !bg-orange-500 hover:!bg-orange-600 shadow-md">
                                <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2" /> GERAR PDF
                            </x-button>
                        </div>
                    </form>
                </div>
            </x-card>

        </div>
    </div>
</x-app-layout>
