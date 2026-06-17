<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            {{ __('Diário') }} - {{ $eletiva->nome }}
            <span class="ml-3 px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-md {{ $eletiva->tipo == 'eletiva' ? 'bg-indigo-100 text-indigo-800' : 'bg-purple-100 text-purple-800' }}">
                {{ ucfirst($eletiva->tipo) }}
            </span>
        </div>
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Eletivas', 'url' => route('eletivas.index')],
            ['label' => 'Diário']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        @if (session('success'))
            <x-alert type="success" message="{{ session('success') }}" />
        @endif
        @if (session('error'))
            <x-alert type="error" message="{{ session('error') }}" />
        @endif

        @if($errors->any())
            <x-alert type="error">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <!-- Tabs -->
        @php
            $tab = request('tab', 'frequencia');
        @endphp
        <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-200 inline-flex space-x-1 w-full sm:w-auto">
            <a href="{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'frequencia']) }}" 
                class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all flex-1 sm:flex-none text-center {{ $tab == 'frequencia' ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <div class="flex items-center justify-center">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5 mr-2" />
                    Frequência
                </div>
            </a>
            
            @if($eletiva->usa_nota)
            <a href="{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas']) }}" 
                class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all flex-1 sm:flex-none text-center {{ $tab == 'notas' ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <div class="flex items-center justify-center">
                    <x-heroicon-o-document-chart-bar class="w-5 h-5 mr-2" />
                    Notas
                </div>
            </a>
            @endif
        </div>

        <x-card class="border-t-4 border-t-primary-500">
            
            @if($tab == 'frequencia')
            <!-- ABA FREQUÊNCIA -->
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900">Lançamento de Frequência</h3>
            </x-slot>

            <form action="{{ route('eletivas.diario.show', $eletiva->id) }}" method="GET" class="mb-6 flex flex-col sm:flex-row items-end gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <input type="hidden" name="tab" value="frequencia">
                <div class="w-full sm:w-auto">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Data da Aula</label>
                    <x-input type="date" name="data" value="{{ $dataSelecionada }}" class="w-full sm:w-64" />
                </div>
                <x-button variant="secondary" type="submit" class="w-full sm:w-auto justify-center">
                    <x-heroicon-o-arrow-path class="w-5 h-5 mr-2" /> Carregar
                </x-button>
            </form>

            <form action="{{ route('eletivas.diario.frequencia', $eletiva->id) }}" method="POST">
                @csrf
                <input type="hidden" name="data" value="{{ $dataSelecionada }}">

                <div class="-mx-6 -my-6 sm:mx-0 sm:my-0 sm:rounded-lg overflow-hidden border border-gray-200">
                    <x-table>
                        <x-slot name="head">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">Estudante</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Presente (P)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Falta (F)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Falta Justificada (FJ)</th>
                        </x-slot>
                        <x-slot name="body">
                            @forelse($eletiva->alunosAtivos as $aluno)
                                @php
                                    $statusAtual = isset($frequencias[$aluno->id]) ? $frequencias[$aluno->id]->status : 'P';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        {{ $aluno->nome }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="radio" name="frequencia[{{ $aluno->id }}]" value="P" {{ $statusAtual == 'P' ? 'checked' : '' }} class="text-green-600 focus:ring-green-500 w-6 h-6 cursor-pointer border-gray-300">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="radio" name="frequencia[{{ $aluno->id }}]" value="F" {{ $statusAtual == 'F' ? 'checked' : '' }} class="text-red-600 focus:ring-red-500 w-6 h-6 cursor-pointer border-gray-300">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="radio" name="frequencia[{{ $aluno->id }}]" value="FJ" {{ $statusAtual == 'FJ' ? 'checked' : '' }} class="text-yellow-600 focus:ring-yellow-500 w-6 h-6 cursor-pointer border-gray-300">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">
                                        <x-heroicon-o-users class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                                        Nenhum aluno inscrito nesta disciplina.
                                    </td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-table>
                </div>

                @if($eletiva->alunosAtivos->count() > 0)
                <div class="mt-6 flex justify-end">
                    <x-button variant="primary" type="submit">
                        <x-heroicon-o-check class="w-5 h-5 mr-2" /> Salvar Chamada
                    </x-button>
                </div>
                @endif
            </form>
            
            @elseif($tab == 'notas' && $eletiva->usa_nota)
            <!-- ABA NOTAS -->
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900">Lançamento de Notas</h3>
            </x-slot>

            <form action="{{ route('eletivas.diario.notas', $eletiva->id) }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-purple-50 p-6 rounded-xl mb-6 border border-purple-100">
                    <div>
                        <label class="block text-sm font-bold text-purple-900 uppercase tracking-wider mb-2">Data da Avaliação</label>
                        <x-input type="date" name="data_avaliacao" required value="{{ old('data_avaliacao', $dataAvaliacao) }}" :readonly="request('action') == 'ver'" class="w-full border-purple-300 focus:border-purple-500 focus:ring-purple-500 {{ request('action') == 'ver' ? 'bg-purple-100 text-purple-700 font-bold' : '' }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-purple-900 uppercase tracking-wider mb-2">Descrição (Ex: Trabalho Prático, Prova)</label>
                        <x-input type="text" name="descricao" required placeholder="Nome da avaliação" value="{{ old('descricao', $descricaoAvaliacao) }}" :readonly="request('action') == 'ver'" class="w-full border-purple-300 focus:border-purple-500 focus:ring-purple-500 {{ request('action') == 'ver' ? 'bg-purple-100 text-purple-700 font-bold' : '' }}" />
                    </div>
                </div>

                <div class="-mx-6 -my-6 sm:mx-0 sm:my-0 sm:rounded-lg overflow-hidden border border-gray-200">
                    <x-table>
                        <x-slot name="head">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">Estudante</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nota (0 a 100)</th>
                        </x-slot>
                        <x-slot name="body">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        {{ $aluno->nome }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <x-input type="number" name="notas[{{ $aluno->id }}]" min="0" max="100" step="0.01" placeholder="-" value="{{ old('notas.'.$aluno->id, $notaAtual) }}" :readonly="request('action') == 'ver'" class="w-24 text-center font-bold text-lg {{ request('action') == 'ver' ? 'bg-gray-100 text-gray-500' : '' }}" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-12 text-center text-gray-500 italic">
                                        <x-heroicon-o-users class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                                        Nenhum aluno inscrito nesta disciplina.
                                    </td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-table>
                </div>

                @if($eletiva->alunosAtivos->count() > 0 && request('action') != 'ver')
                <div class="mt-6 flex justify-end gap-3">
                    @if(request('action') == 'editar')
                        <x-button variant="secondary" type="button" onclick="window.location='{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas']) }}'">
                            Cancelar
                        </x-button>
                    @endif
                    <x-button variant="primary" type="submit">
                        <x-heroicon-o-check class="w-5 h-5 mr-2" /> Salvar Notas
                    </x-button>
                </div>
                @elseif(request('action') == 'ver')
                <div class="mt-6 flex justify-end">
                    <x-button variant="primary" type="button" onclick="window.location='{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas']) }}'">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" /> Lançar Nova Avaliação
                    </x-button>
                </div>
                @endif
            </form>

            <!-- Histórico de Avaliações -->
            @if($avaliacoes->count() > 0)
            <div class="mt-12">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <x-heroicon-o-clock class="w-5 h-5 mr-2 text-gray-500" />
                    Histórico de Lançamentos
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($avaliacoes as $aval)
                        <div class="bg-white border-2 border-dashed border-gray-200 p-5 rounded-xl hover:border-purple-300 hover:shadow-md transition-all flex flex-col justify-between group">
                            <div>
                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 uppercase tracking-wider mb-3">
                                    {{ \Carbon\Carbon::parse($aval->data)->format('d/m/Y') }}
                                </div>
                                <div class="font-black text-gray-900 text-lg leading-tight">{{ $aval->descricao }}</div>
                            </div>
                            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas', 'data_avaliacao' => \Carbon\Carbon::parse($aval->data)->format('Y-m-d'), 'descricao' => $aval->descricao, 'action' => 'ver']) }}" class="text-primary-600 hover:text-primary-800 text-sm font-bold flex items-center bg-primary-50 px-3 py-1.5 rounded-lg transition-colors">
                                    <x-heroicon-o-eye class="w-4 h-4 mr-1" /> Ver Notas
                                </a>
                                <a href="{{ route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas', 'data_avaliacao' => \Carbon\Carbon::parse($aval->data)->format('Y-m-d'), 'descricao' => $aval->descricao, 'action' => 'editar']) }}" class="text-amber-600 hover:text-amber-800 text-sm font-bold flex items-center bg-amber-50 px-3 py-1.5 rounded-lg transition-colors">
                                    <x-heroicon-o-pencil-square class="w-4 h-4 mr-1" /> Editar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endif
        </x-card>
    </div>
</x-app-layout>
