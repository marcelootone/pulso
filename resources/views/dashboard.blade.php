<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel de Gestão Estratégica - SIGAE') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex flex-wrap gap-4">
                @hasrole('Gestor|Secretaria')
                <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-black font-semibold py-2 px-4 rounded shadow flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Criar Usuário
                </a>
                <a href="{{ route('importar.index') }}" class="bg-white hover:bg-gray-50 text-blue-600 border border-gray-300 font-semibold py-2 px-4 rounded shadow flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Importar Estudantes
                </a>
                @endhasrole

                @unlessrole('Estudante')
                <a href="{{ route('turmas.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-semibold py-2 px-4 rounded shadow flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Turmas
                </a>
                @endunlessrole

                @hasrole('Gestor|Secretaria|Coordenador')
                <a href="{{ route('users.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-semibold py-2 px-4 rounded shadow flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Funcionários
                </a>
                <a href="{{ route('atribuicoes.create') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-semibold py-2 px-4 rounded shadow flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Atribuir Aulas
                </a>
                @endhasrole

                @hasrole('Gestor|Secretaria')
                <a href="{{ route('vinculo.create') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-semibold py-2 px-4 rounded shadow flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    Vincular Aluno a Turma
                </a>
                @endhasrole

                @hasrole('Gestor|Coordenador|Professor|Professor Educação Especial|Professor de Estudo Orientado')
                <a href="{{ route('diario.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-semibold py-2 px-4 rounded shadow flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Meu Diário
                </a>
                @endhasrole

                @auth
                <a href="{{ route('agendamentos.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-semibold py-2 px-4 rounded shadow flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Agendamentos
                </a>
                <a href="{{ route('espacos.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-semibold py-2 px-4 rounded shadow flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Espaços
                </a>
                @endauth

                @hasrole('Gestor|Coordenador|Professor|Professor Educação Especial|Professor de Estudo Orientado')
                <a href="{{ route('planejamento.index') }}" id="btn-planejamento-semanal" class="bg-emerald-600 hover:bg-emerald-700 text-black font-semibold py-2 px-4 rounded shadow flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Planejamento Semanal
                </a>
                @endhasrole
            </div>

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
                        <a href="{{ route('relatorios.evasao') }}" class="bg-red-600 hover:bg-red-700 text-black text-sm font-bold py-2 px-4 rounded shadow">
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