<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes do Encaminhamento - Análise') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Informações Gerais</h3>
                        <p><strong>Aluno:</strong> {{ $solicitacao->aluno->nome }}</p>
                        <p><strong>Turma:</strong> {{ $solicitacao->turma->serie }} {{ $solicitacao->turma->complemento ?? '' }}</p>
                        <p><strong>Professor Solicitante:</strong> {{ $solicitacao->solicitante->name }} ({{ $solicitacao->disciplina_solicitante }})</p>
                        <p><strong>Data da Solicitação:</strong> {{ $solicitacao->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mt-2">
                            <strong>Status Atual:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $solicitacao->status === 'Pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $solicitacao->status === 'Aprovada' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $solicitacao->status === 'Rejeitada' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $solicitacao->status === 'EmAtendimento' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                {{ $solicitacao->status === 'Concluida' ? 'bg-green-100 text-green-800' : '' }}
                            ">
                                {{ $solicitacao->status }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Motivo / Justificativa</h3>
                        <p class="bg-gray-50 p-4 rounded-md border text-gray-700 whitespace-pre-wrap">{{ $solicitacao->motivo }}</p>
                    </div>
                </div>
            </div>

            <!-- Ações do Coordenador -->
            @can('analisar', $solicitacao)
                @if($solicitacao->status === 'Pendente')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Análise da Coordenação</h3>
                        
                        <form method="POST" action="{{ route('estudo-orientado.analises.store', $solicitacao->id) }}">
                            @csrf
                            
                            <div class="mb-4">
                                <x-input-label for="acao" :value="__('Ação')" />
                                <select id="acao" name="acao" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="" disabled selected>Selecione uma ação</option>
                                    <option value="aprovar">Aprovar Encaminhamento</option>
                                    <option value="rejeitar">Rejeitar/Devolver</option>
                                </select>
                                <x-input-error :messages="$errors->get('acao')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="parecer" :value="__('Parecer / Justificativa')" />
                                <textarea id="parecer" name="parecer" rows="4" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('parecer') }}</textarea>
                                <x-input-error :messages="$errors->get('parecer')" class="mt-2" />
                            </div>

                            <div class="flex justify-end mt-4">
                                <x-primary-button>Registrar Análise</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                @if(in_array($solicitacao->status, ['Aprovada', 'EmAtendimento']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Atribuição de Orientador</h3>
                        
                        <form method="POST" action="{{ route('estudo-orientado.analises.store', $solicitacao->id) }}">
                            @csrf
                            <input type="hidden" name="acao" value="atribuir">
                            
                            <div class="mb-4">
                                <x-input-label for="professor_orientador_id" :value="__('Professor de Estudo Orientado')" />
                                <div class="flex gap-4 items-start">
                                    <select id="professor_orientador_id" name="professor_orientador_id" required class="mt-1 block w-full md:w-1/2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="" disabled selected>Selecione um Professor</option>
                                        @foreach($orientadores as $orientador)
                                            <option value="{{ $orientador->id }}" {{ $solicitacao->professor_orientador_id == $orientador->id ? 'selected' : '' }}>
                                                {{ $orientador->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-primary-button class="mt-1 whitespace-nowrap">Atribuir Orientador</x-primary-button>
                                </div>
                                <x-input-error :messages="$errors->get('professor_orientador_id')" class="mt-2" />
                            </div>
                        </form>

                        @if($solicitacao->professor_orientador_id)
                            <div class="mt-4 p-4 bg-indigo-50 rounded-md text-indigo-700 border border-indigo-200">
                                <strong>Orientador atual:</strong> {{ $solicitacao->orientador->name ?? 'N/A' }}
                                <br>
                                <span class="text-xs">Atribuído em: {{ $solicitacao->data_atribuicao?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            @endcan

            <!-- Histórico -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Histórico do Encaminhamento</h3>
                    
                    @if($solicitacao->historicos->count() > 0)
                        <ul class="space-y-6 ml-2 mt-4">
                            @foreach($solicitacao->historicos->sortByDesc('created_at') as $hist)
                                <li class="relative flex gap-4">
                                    <!-- Vertical line connecting to next item -->
                                    @if(!$loop->last)
                                    <div class="absolute left-4 top-8 -bottom-6 w-px bg-gray-200"></div>
                                    @endif
                                    
                                    <!-- Icon -->
                                    <div class="relative z-10 flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full shrink-0 ring-4 ring-white">
                                        <x-heroicon-s-check-circle class="w-5 h-5 text-blue-800" />
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="flex-1 pb-1">
                                        <h4 class="text-md font-semibold text-gray-900 leading-tight">
                                            {{ ucfirst(str_replace('_', ' ', $hist->acao)) }}
                                        </h4>
                                        <time class="block mt-1 mb-2 text-sm font-normal text-gray-400">
                                            {{ $hist->created_at->format('d/m/Y H:i') }} por {{ $hist->user->name ?? 'Sistema' }}
                                        </time>
                                        <p class="text-sm font-normal text-gray-600 whitespace-pre-wrap">{{ $hist->descricao }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-sm">Nenhum histórico registrado.</p>
                    @endif
                </div>
            </div>

            <div class="text-center pb-8">
                <a href="{{ route('estudo-orientado.analises.index') }}" class="text-indigo-600 hover:underline">Voltar para a lista</a>
            </div>

        </div>
    </div>
</x-app-layout>
