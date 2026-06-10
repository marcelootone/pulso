<x-app-layout>
    <x-slot name="header">
        {{ __('Painel de Gestão Estratégica') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Dashboard']
        ]" />
    </x-slot>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-card class="border-l-4 border-l-primary-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary-100 text-primary-600 mr-4">
                    <x-heroicon-o-users class="w-8 h-8" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Estudantes</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalAlunos }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="border-l-4 border-l-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <x-heroicon-o-academic-cap class="w-8 h-8" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Turmas</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalTurmas }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="border-l-4 {{ $mediaEscola < 75 ? 'border-l-red-500' : 'border-l-green-500' }}">
            <div class="flex items-center">
                <div class="p-3 rounded-full {{ $mediaEscola < 75 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} mr-4">
                    <x-heroicon-o-chart-bar class="w-8 h-8" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Freq. Geral</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($mediaEscola, 1) }}%</p>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Alunos em Risco -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-500 mr-2" />
                        Alerta de Evasão
                    </h3>
                    <x-button variant="danger" size="sm" onclick="window.location='{{ route('relatorios.evasao') }}'">
                        Baixar PDF
                    </x-button>
                </div>
            </x-slot>

            <div class="-mx-6 -my-6">
                <x-table>
                    <x-slot name="head">
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudante</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turma</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Presença</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($alunosEmRisco as $aluno)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $aluno->nome }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $aluno->serie }}º {{ $aluno->complemento }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-semibold">{{ number_format($aluno->percentual, 1) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 italic">Nenhum aluno em risco crítico.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-table>
            </div>
        </x-card>

        <!-- Gráfico -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900">Saúde Escolar por Bimestre</h3>
            </x-slot>
            <canvas id="evasaoChart" height="200"></canvas>
        </x-card>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('evasaoChart');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['1º Bim', '2º Bim', '3º Bim', '4º Bim'],
                        datasets: [{
                            label: 'Frequência Média (%)',
                            data: [92, 88, {{ number_format($mediaEscola, 0) }}, 0],
                            backgroundColor: 'rgba(79, 70, 229, 0.5)',
                            borderColor: '#4F46E5',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: { 
                        scales: { 
                            y: { 
                                beginAtZero: true, 
                                max: 100 
                            } 
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>