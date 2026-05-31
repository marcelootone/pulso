<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Diário') }} - {{ $eletiva->nome }}
            </h2>
            <a href="{{ route('eletivas.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">&larr; Voltar às Eletivas</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tabs -->
            @php
                $tab = request('tab', 'frequencia');
            @endphp
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'frequencia']) }}" 
                       class="{{ $tab == 'frequencia' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Lançamento de Frequência
                    </a>
                    
                    @if($eletiva->usa_nota)
                    <a href="{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas']) }}" 
                       class="{{ $tab == 'notas' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Lançamento de Notas
                    </a>
                    @endif
                </nav>
            </div>

            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl border border-gray-100">
                
                @if($tab == 'frequencia')
                <!-- ABA FREQUÊNCIA -->
                <div class="p-6">
                    <form action="{{ route('eletivas.diario.show', $eletiva->id) }}" method="GET" class="mb-6 flex items-end gap-4 bg-gray-50 p-4 rounded-lg">
                        <input type="hidden" name="tab" value="frequencia">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data da Aula</label>
                            <input type="date" name="data" value="{{ $dataSelecionada }}" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">Carregar Lista</button>
                    </form>

                    <form action="{{ route('eletivas.diario.frequencia', $eletiva->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="data" value="{{ $dataSelecionada }}">

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">Estudante</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Presente (P)</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Falta (F)</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Falta Justificada (FJ)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($eletiva->alunosAtivos as $aluno)
                                        @php
                                            $statusAtual = isset($frequencias[$aluno->id]) ? $frequencias[$aluno->id]->status : 'P';
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $aluno->nome }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input type="radio" name="frequencia[{{ $aluno->id }}]" value="P" {{ $statusAtual == 'P' ? 'checked' : '' }} class="text-green-600 focus:ring-green-500 w-5 h-5 cursor-pointer">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input type="radio" name="frequencia[{{ $aluno->id }}]" value="F" {{ $statusAtual == 'F' ? 'checked' : '' }} class="text-red-600 focus:ring-red-500 w-5 h-5 cursor-pointer">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input type="radio" name="frequencia[{{ $aluno->id }}]" value="FJ" {{ $statusAtual == 'FJ' ? 'checked' : '' }} class="text-yellow-600 focus:ring-yellow-500 w-5 h-5 cursor-pointer">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Nenhum aluno inscrito nesta disciplina.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($eletiva->alunosAtivos->count() > 0)
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-black font-bold py-2 px-6 rounded-lg shadow transition">
                                Salvar Chamada
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
                
                @elseif($tab == 'notas' && $eletiva->usa_nota)
                <!-- ABA NOTAS -->
                <div class="p-6">
                    <form action="{{ route('eletivas.diario.notas', $eletiva->id) }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-lg mb-6 border border-gray-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data da Avaliação</label>
                                <input type="date" name="data_avaliacao" required value="{{ old('data_avaliacao', $dataAvaliacao) }}" {{ request('action') == 'ver' ? 'readonly' : '' }} class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ request('action') == 'ver' ? 'bg-gray-100' : '' }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição (Ex: Trabalho Prático, Prova)</label>
                                <input type="text" name="descricao" required placeholder="Nome da avaliação" value="{{ old('descricao', $descricaoAvaliacao) }}" {{ request('action') == 'ver' ? 'readonly' : '' }} class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ request('action') == 'ver' ? 'bg-gray-100' : '' }}">
                            </div>
                        </div>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">Estudante</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nota (0 a 100)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($eletiva->alunosAtivos as $aluno)
                                        @php
                                            $notaAtual = '';
                                            if (isset($notas[$aluno->id]) && $dataAvaliacao && $descricaoAvaliacao) {
                                                $notaModel = $notas[$aluno->id]->first(function($nota) use ($dataAvaliacao, $descricaoAvaliacao) {
                                                    return $nota->data->format('Y-m-d') == $dataAvaliacao && $nota->descricao == $descricaoAvaliacao;
                                                });
                                                if ($notaModel) {
                                                    $notaAtual = $notaModel->nota;
                                                }
                                            }
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 flex items-center h-full">
                                                {{ $aluno->nome }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input type="number" name="notas[{{ $aluno->id }}]" min="0" max="100" step="0.01" placeholder="-" value="{{ old('notas.'.$aluno->id, $notaAtual) }}" {{ request('action') == 'ver' ? 'readonly' : '' }} class="w-24 text-center border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-semibold text-gray-800 {{ request('action') == 'ver' ? 'bg-gray-100 text-gray-500' : '' }}">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-6 py-8 text-center text-gray-500">Nenhum aluno inscrito nesta disciplina.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($eletiva->alunosAtivos->count() > 0 && request('action') != 'ver')
                        <div class="mt-6 flex justify-end gap-2">
                            @if(request('action') == 'editar')
                                <a href="{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas']) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg shadow transition">
                                    Cancelar
                                </a>
                            @endif
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-black font-bold py-2 px-6 rounded-lg shadow transition">
                                Salvar Notas
                            </button>
                        </div>
                        @elseif(request('action') == 'ver')
                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas']) }}" class="bg-indigo-600 hover:bg-indigo-700 text-black font-bold py-2 px-6 rounded-lg shadow transition">
                                Lançar Nova Avaliação
                            </a>
                        </div>
                        @endif
                    </form>

                    <!-- Histórico de Avaliações -->
                    @if($avaliacoes->count() > 0)
                    <div class="mt-12">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Histórico de Lançamentos de Notas</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($avaliacoes as $aval)
                                <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition flex flex-col justify-between">
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">{{ \Carbon\Carbon::parse($aval->data)->format('d/m/Y') }}</div>
                                        <div class="font-semibold text-gray-800">{{ $aval->descricao }}</div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end space-x-3">
                                        <a href="{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas', 'data_avaliacao' => \Carbon\Carbon::parse($aval->data)->format('Y-m-d'), 'descricao' => $aval->descricao, 'action' => 'ver']) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Ver Notas
                                        </a>
                                        <a href="{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas', 'data_avaliacao' => \Carbon\Carbon::parse($aval->data)->format('Y-m-d'), 'descricao' => $aval->descricao, 'action' => 'editar']) }}" class="text-amber-500 hover:text-amber-700 text-sm font-medium transition-colors flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Editar
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                
            </div>
        </div>
    </div>
</x-app-layout>
