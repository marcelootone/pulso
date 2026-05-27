<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            {{ __('Busca Ativa (Alerta de Frequência)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Navegação Secundária --}}
            <div class="mb-6 flex space-x-4">
                <a href="{{ route('frequencia.index') }}" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 rounded-md font-bold shadow transition">Visão Geral</a>
                <a href="{{ route('frequencia.monitorar') }}" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 rounded-md font-bold shadow transition">Lançar Chamada</a>
                <a href="{{ route('frequencia.busca_ativa') }}" class="px-4 py-2 bg-red-600 text-white rounded-md font-bold shadow transition">Busca Ativa (Faltas)</a>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filtro --}}
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border-l-4 border-red-500 flex flex-col md:flex-row items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-lg font-bold text-gray-800">Alunos com Menos de 75% de Frequência</h3>
                    <p class="text-sm text-gray-500">Mês de referência: {{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}/{{ $ano }}</p>
                </div>
                
                <form method="GET" action="{{ route('frequencia.busca_ativa') }}" class="flex space-x-4 items-end">
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
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Filtrar Turma</label>
                        <select name="turma_id" class="rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">Todas as Turmas</option>
                            @foreach($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ $turmaId == $turma->id ? 'selected' : '' }}>
                                    {{ $turma->serie }}º {{ $turma->complemento }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded shadow font-bold text-sm">Filtrar</button>
                </form>
            </div>

            {{-- Lista de Alunos em Risco --}}
            <div class="space-y-6">
                @forelse($alunosRisco as $risco)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-red-100">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b pb-4 mb-4">
                                <div>
                                    <h4 class="text-xl font-black text-gray-800">{{ $risco->aluno->nome }}</h4>
                                    <p class="text-sm font-semibold text-gray-600 mt-1">Turma: {{ $risco->turma->serie }}º {{ $risco->turma->complemento }}</p>
                                </div>
                                <div class="mt-4 md:mt-0 text-right">
                                    <div class="inline-block px-4 py-2 bg-red-50 rounded-lg border border-red-200">
                                        <p class="text-xs uppercase font-bold text-red-800">Frequência Mensal</p>
                                        <p class="text-2xl font-black text-red-600">{{ number_format($risco->percentual, 1) }}%</p>
                                        <p class="text-xs font-medium text-red-700 mt-1">{{ $risco->total_faltas }} faltas no mês</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Histórico de Contatos --}}
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <h5 class="font-bold text-gray-700 mb-3 text-sm uppercase flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Histórico de Busca Ativa neste mês
                                    </h5>
                                    
                                    @if($risco->registros->count() > 0)
                                        <div class="space-y-3 max-h-48 overflow-y-auto pr-2">
                                            @foreach($risco->registros as $registro)
                                                <div class="bg-white p-3 rounded shadow-sm border border-gray-100 text-sm">
                                                    <div class="flex justify-between items-start mb-1">
                                                        <strong class="text-gray-800">{{ \Carbon\Carbon::parse($registro->data)->format('d/m/Y') }}</strong>
                                                        <span class="text-xs text-gray-500">Por: {{ $registro->user->name }}</span>
                                                    </div>
                                                    <p class="text-gray-700 italic">"{{ $registro->observacao }}"</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 italic text-center py-4">Nenhuma ação de busca ativa registrada para este aluno neste mês.</p>
                                    @endif
                                </div>

                                {{-- Formulário de Novo Contato --}}
                                <div>
                                    <h5 class="font-bold text-gray-700 mb-3 text-sm uppercase">Registrar Nova Ação</h5>
                                    <form action="{{ route('frequencia.registrar_busca_ativa') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="aluno_id" value="{{ $risco->aluno->id }}">
                                        
                                        <div class="mb-3">
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Data da Ação</label>
                                            <input type="date" name="data" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-red-500 focus:ring focus:ring-red-200">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Observação / Medida Tomada *</label>
                                            <textarea name="observacao" rows="3" required placeholder="Ex: Entramos em contato com a mãe pelo WhatsApp. Ela informou que o aluno esteve doente." class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-red-500 focus:ring focus:ring-red-200"></textarea>
                                        </div>
                                        
                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
                                            Salvar Registro
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-500 p-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Tudo em ordem!</h3>
                        <p class="text-gray-600">Nenhum aluno está com a frequência abaixo de 75% com os filtros selecionados.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
