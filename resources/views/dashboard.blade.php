<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel de Gestão Estratégica - SIGAE') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-sm font-bold text-gray-500 uppercase">Total de Estudantes</p>
                    <p class="text-3xl font-black text-gray-800">{{ $totalAlunos }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500">
                    <p class="text-sm font-bold text-gray-500 uppercase">Turmas Ativas</p>
                    <p class="text-3xl font-black text-gray-800">{{ $totalTurmas }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-l-4 {{ $mediaEscola < 75 ? 'border-red-500' : 'border-green-500' }}">
                    <p class="text-sm font-bold text-gray-500 uppercase">Frequência Geral</p>
                    <p class="text-3xl font-black text-gray-800">{{ number_format($mediaEscola, 1) }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-red-600 flex items-center">
                            ⚠️ Alerta de Evasão
                        </h3>
                        <a href="{{ route('relatorios.evasao') }}" class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-2 px-4 rounded shadow">
                            📄 BAIXAR PDF
                        </a>
                    </div>
                    
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs uppercase text-gray-400 border-b">
                                <th class="pb-2">Estudante</th>
                                <th class="pb-2">Turma</th>
                                <th class="pb-2">Presença</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alunosEmRisco as $aluno)
                            <tr class="border-b">
                                <td class="py-3 font-medium">{{ $aluno->nome }}</td>
                                <td class="py-3 text-gray-600">{{ $aluno->serie }}º {{ $aluno->complemento }}</td>
                                <td class="py-3 text-red-600 font-bold">{{ number_format($aluno->percentual, 1) }}%</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-500 italic">Nenhum aluno em risco crítico no momento.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Saúde Escolar por Bimestre</h3>
                    <canvas id="evasaoChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('evasaoChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Fev', 'Mar', 'Abr', 'Mai'],
                datasets: [{
                    label: 'Frequência Média (%)',
                    data: [92, 88, {{ number_format($mediaEscola, 0) }}, 0],
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: { scales: { y: { beginAtZero: true, max: 100 } } }
        });
    </script>
</x-app-layout>