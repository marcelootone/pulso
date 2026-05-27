<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            {{ __('Monitoramento Escolar (Coordenador)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Navegação Secundária do Módulo --}}
            <div class="mb-6 flex space-x-4">
                <a href="{{ route('frequencia.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-bold shadow transition">Visão Geral</a>
                <a href="{{ route('frequencia.monitorar') }}" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 rounded-md font-bold shadow transition">Lançar Chamada</a>
                <a href="{{ route('frequencia.busca_ativa') }}" class="px-4 py-2 bg-white text-red-600 hover:bg-red-50 border border-gray-300 rounded-md font-bold shadow transition">Busca Ativa (Faltas)</a>
            </div>

            {{-- Filtro de Mês/Ano --}}
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6 flex items-center justify-between">
                <form method="GET" action="{{ route('frequencia.index') }}" class="flex space-x-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mês</label>
                        <select name="mes" class="rounded-md border-gray-300 shadow-sm text-sm">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Ano</label>
                        <select name="ano" class="rounded-md border-gray-300 shadow-sm text-sm">
                            @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                <option value="{{ $i }}" {{ $ano == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded shadow font-bold text-sm">Filtrar</button>
                </form>
            </div>

            {{-- Grid de Turmas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($turmas as $turma)
                    <div class="bg-white rounded-lg shadow overflow-hidden border-t-4 {{ $turma->percentual_frequencia < 75 ? 'border-red-500' : 'border-green-500' }}">
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-black text-gray-800">{{ $turma->serie }}º {{ $turma->complemento }}</h3>
                                    <p class="text-sm text-gray-500 font-semibold">{{ $turma->modalidade }} - {{ $turma->turno }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-3xl font-black {{ $turma->percentual_frequencia < 75 ? 'text-red-500' : 'text-green-500' }}">
                                        {{ number_format($turma->percentual_frequencia, 1) }}%
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 uppercase font-bold">Dias Letivos Registrados no Mês</p>
                                <p class="text-lg font-medium text-gray-700">{{ $turma->dias_letivos_registrados }} dias</p>
                            </div>

                            <a href="{{ route('frequencia.monitorar', ['turma_id' => $turma->id]) }}" class="block w-full text-center bg-gray-50 hover:bg-indigo-50 text-indigo-700 font-bold py-2 px-4 rounded border border-gray-200 transition">
                                Acessar Chamada
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 bg-white rounded shadow text-gray-500">
                        Nenhuma turma ativa encontrada para monitoramento.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
