<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Relatórios e Indicadores - Estudo Orientado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-6 text-gray-700">Visão Geral de Encaminhamentos</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 flex flex-col items-center justify-center text-center">
                            <span class="text-3xl font-bold text-blue-700">{{ $stats['total'] }}</span>
                            <span class="text-sm font-semibold text-blue-800 mt-2 uppercase tracking-wide">Total Geral</span>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 flex flex-col items-center justify-center text-center">
                            <span class="text-3xl font-bold text-yellow-700">{{ $stats['pendentes'] }}</span>
                            <span class="text-sm font-semibold text-yellow-800 mt-2 uppercase tracking-wide">Pendentes</span>
                        </div>
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6 flex flex-col items-center justify-center text-center">
                            <span class="text-3xl font-bold text-indigo-700">{{ $stats['em_atendimento'] }}</span>
                            <span class="text-sm font-semibold text-indigo-800 mt-2 uppercase tracking-wide">Em Atendimento</span>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 flex flex-col items-center justify-center text-center">
                            <span class="text-3xl font-bold text-green-700">{{ $stats['concluidas'] }}</span>
                            <span class="text-sm font-semibold text-green-800 mt-2 uppercase tracking-wide">Concluídas</span>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Ações Administrativas</h3>
                    <div class="flex gap-4 mt-4">
                        <a href="{{ route('estudo-orientado.analises.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Acessar Painel de Análise
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
