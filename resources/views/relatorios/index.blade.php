<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ __('Central de Relatórios (PDF)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                {{-- Relatório 1: Alerta de Evasão --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 flex flex-col">
                    <div class="p-6 flex-grow border-t-4 border-red-500">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-red-100 rounded-full text-red-600 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Alerta de Evasão</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">Gera um relatório completo com todos os alunos da escola que estão com a frequência global abaixo de 75%.</p>
                    </div>
                    <div class="bg-gray-50 p-4 border-t">
                        <a href="{{ route('relatorios.evasao') }}" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition">
                            BAIXAR PDF
                        </a>
                    </div>
                </div>

                {{-- Relatório 2: Frequência Mensal --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 flex flex-col">
                    <div class="p-6 flex-grow border-t-4 border-blue-500">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-blue-100 rounded-full text-blue-600 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Frequência da Turma</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">Mapa detalhado dia-a-dia de presença, falta e falta justificada de todos os alunos de uma turma em um mês específico.</p>
                        
                        <form action="{{ route('relatorios.frequencia_mensal') }}" method="GET" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Turma</label>
                                <select name="turma_id" class="w-full rounded-md border-gray-300 text-sm" required>
                                    <option value="">Selecione...</option>
                                    @foreach($turmas as $turma)
                                        <option value="{{ $turma->id }}">{{ $turma->serie }}º {{ $turma->complemento }} ({{ $turma->turno }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mês</label>
                                    <select name="mes" class="w-full rounded-md border-gray-300 text-sm" required>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Ano</label>
                                    <select name="ano" class="w-full rounded-md border-gray-300 text-sm" required>
                                        @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="pt-2">
                                <button type="submit" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                                    GERAR PDF (PAISAGEM)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Relatório 3: Ranking de Faltas --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 flex flex-col">
                    <div class="p-6 flex-grow border-t-4 border-orange-500">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-orange-100 rounded-full text-orange-600 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Ranking de Faltas</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">Lista as turmas ordenadas pelo índice de ausência (turmas que mais faltam aparecem primeiro) em determinado mês.</p>
                        
                        <form action="{{ route('relatorios.turmas_faltas') }}" method="GET" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mês</label>
                                    <select name="mes" class="w-full rounded-md border-gray-300 text-sm" required>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Ano</label>
                                    <select name="ano" class="w-full rounded-md border-gray-300 text-sm" required>
                                        @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="pt-2">
                                <button type="submit" class="w-full text-center bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded shadow transition">
                                    GERAR RELATÓRIO
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
