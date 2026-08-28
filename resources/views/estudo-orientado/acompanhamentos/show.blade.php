<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Prontuário de Acompanhamento: ') }} {{ $solicitacao->aluno->nome }}
            </h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                {{ $solicitacao->status === 'Aprovada' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $solicitacao->status === 'EmAtendimento' ? 'bg-indigo-100 text-indigo-800' : '' }}
                {{ $solicitacao->status === 'Concluida' ? 'bg-green-100 text-green-800' : '' }}
            ">
                {{ $solicitacao->status }}
            </span>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'resumo' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Navegação de Abas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button @click="tab = 'resumo'" :class="tab === 'resumo' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Resumo e Motivo
                        </button>
                        <button @click="tab = 'atendimentos'" :class="tab === 'atendimentos' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Atendimentos (Sessões)
                        </button>
                        <button @click="tab = 'planos'" :class="tab === 'planos' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Planos de Ação
                        </button>
                        <button @click="tab = 'evolucoes'" :class="tab === 'evolucoes' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Evoluções
                        </button>
                        <button @click="tab = 'concluir'" :class="tab === 'concluir' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Concluir Acompanhamento
                        </button>
                    </nav>
                </div>
            </div>

            <!-- CONTEÚDO DAS ABAS -->

            <!-- ABA: Resumo -->
            <div x-show="tab === 'resumo'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Detalhes do Encaminhamento</h3>
                        <p><strong>Turma:</strong> {{ $solicitacao->turma->serie }} {{ $solicitacao->turma->complemento ?? '' }}</p>
                        <p><strong>Professor Solicitante:</strong> {{ $solicitacao->solicitante->name }} ({{ $solicitacao->disciplina_solicitante }})</p>
                        <p><strong>Coordenador Responsável:</strong> {{ $solicitacao->coordenador->name ?? 'N/A' }}</p>
                        <p><strong>Prioridade:</strong> {{ $solicitacao->prioridade }}</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Motivo Inicial</h3>
                        <p class="bg-gray-50 p-4 rounded-md border text-gray-700 whitespace-pre-wrap">{{ $solicitacao->motivo }}</p>
                        @if($solicitacao->parecer_coordenador)
                            <h3 class="text-lg font-bold mt-6 mb-4 text-gray-700 border-b pb-2">Parecer da Coordenação</h3>
                            <p class="bg-blue-50 p-4 rounded-md border border-blue-100 text-gray-700 whitespace-pre-wrap">{{ $solicitacao->parecer_coordenador }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ABA: Atendimentos -->
            <div x-show="tab === 'atendimentos'" style="display: none;" class="space-y-6">
                @if($solicitacao->status === 'EmAtendimento' && Auth::user()->can('registrarAtendimento', $solicitacao))
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-700">Registrar Novo Atendimento</h3>
                    <form method="POST" action="{{ route('estudo-orientado.atendimentos.store', $solicitacao->id) }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="data_atendimento" :value="__('Data do Atendimento')" />
                                <x-text-input id="data_atendimento" class="block mt-1 w-full" type="date" name="data_atendimento" value="{{ date('Y-m-d') }}" required />
                            </div>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="descricao_atendimento" :value="__('Descrição (O que foi trabalhado)')" />
                            <textarea id="descricao_atendimento" name="descricao" rows="4" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="observacoes_atendimento" :value="__('Observações Adicionais (Opcional)')" />
                            <textarea id="observacoes_atendimento" name="observacoes" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>
                        <x-primary-button>Salvar Atendimento</x-primary-button>
                    </form>
                </div>
                @endif

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Histórico de Atendimentos</h3>
                    @forelse($solicitacao->atendimentos->sortByDesc('data_atendimento') as $atendimento)
                        <div class="mb-4 pb-4 border-b last:border-0 last:pb-0">
                            <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($atendimento->data_atendimento)->format('d/m/Y') }}</p>
                            <p class="text-gray-700 mt-1">{{ $atendimento->descricao }}</p>
                            @if($atendimento->observacoes)
                                <p class="text-sm text-gray-500 mt-2 bg-gray-50 p-2 rounded"><em>Obs:</em> {{ $atendimento->observacoes }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 italic">Nenhum atendimento registrado ainda.</p>
                    @endforelse
                </div>
            </div>

            <!-- ABA: Planos de Ação -->
            <div x-show="tab === 'planos'" style="display: none;" class="space-y-6">
                @if($solicitacao->status === 'EmAtendimento' && Auth::user()->can('criarPlanoAcao', $solicitacao))
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-700">Novo Plano de Ação</h3>
                    <form method="POST" action="{{ route('estudo-orientado.planos.store', $solicitacao->id) }}">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="descricao_plano" :value="__('Descrição Geral do Plano')" />
                            <textarea id="descricao_plano" name="descricao" rows="2" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="metas" :value="__('Metas (Opcional)')" />
                                <textarea id="metas" name="metas" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            </div>
                            <div>
                                <x-input-label for="estrategias" :value="__('Estratégias (Opcional)')" />
                                <textarea id="estrategias" name="estrategias" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            </div>
                        </div>
                        <div class="w-full md:w-1/3 mb-4">
                            <x-input-label for="prazo" :value="__('Prazo Previsto (Opcional)')" />
                            <x-text-input id="prazo" class="block mt-1 w-full" type="date" name="prazo" />
                        </div>
                        <x-primary-button>Salvar Plano de Ação</x-primary-button>
                    </form>
                </div>
                @endif

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Planos de Ação Registrados</h3>
                    @forelse($solicitacao->planosAcao->sortByDesc('created_at') as $plano)
                        <div class="mb-6 p-4 border rounded-md {{ $plano->status === 'Ativo' ? 'bg-blue-50 border-blue-200' : 'bg-gray-50' }}">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-gray-800">{{ $plano->descricao }}</h4>
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $plano->status === 'Ativo' ? 'bg-blue-200 text-blue-800' : 'bg-gray-200 text-gray-800' }}">{{ $plano->status }}</span>
                            </div>
                            @if($plano->metas)
                                <p class="text-sm text-gray-700 mt-2"><strong>Metas:</strong> <br> {{ $plano->metas }}</p>
                            @endif
                            @if($plano->estrategias)
                                <p class="text-sm text-gray-700 mt-2"><strong>Estratégias:</strong> <br> {{ $plano->estrategias }}</p>
                            @endif
                            @if($plano->prazo)
                                <p class="text-sm text-gray-600 mt-2"><strong>Prazo Previsto:</strong> {{ \Carbon\Carbon::parse($plano->prazo)->format('d/m/Y') }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-4">Criado em: {{ $plano->created_at->format('d/m/Y') }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">Nenhum plano de ação registrado.</p>
                    @endforelse
                </div>
            </div>

            <!-- ABA: Evoluções -->
            <div x-show="tab === 'evolucoes'" style="display: none;" class="space-y-6">
                @if($solicitacao->status === 'EmAtendimento' && Auth::user()->can('registrarEvolucao', $solicitacao))
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-700">Registrar Evolução</h3>
                    <form method="POST" action="{{ route('estudo-orientado.evolucoes.store', $solicitacao->id) }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="data_registro" :value="__('Data da Avaliação')" />
                                <x-text-input id="data_registro" class="block mt-1 w-full" type="date" name="data_registro" value="{{ date('Y-m-d') }}" required />
                            </div>
                            <div>
                                <x-input-label for="indicador" :value="__('Indicador')" />
                                <select id="indicador" name="indicador" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Melhora">Apresentou Melhora</option>
                                    <option value="Estavel" selected>Estável / Manutenção</option>
                                    <option value="Piora">Piora / Necessita nova intervenção</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="descricao_evolucao" :value="__('Parecer / Descrição da Evolução')" />
                            <textarea id="descricao_evolucao" name="descricao" rows="3" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>
                        <x-primary-button>Salvar Evolução</x-primary-button>
                    </form>
                </div>
                @endif

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Histórico de Evolução</h3>
                    @forelse($solicitacao->evolucoes->sortByDesc('data_registro') as $evolucao)
                        <div class="mb-4 p-4 rounded border flex gap-4 items-start
                            {{ $evolucao->indicador === 'Melhora' ? 'bg-green-50 border-green-200' : '' }}
                            {{ $evolucao->indicador === 'Estavel' ? 'bg-gray-50 border-gray-200' : '' }}
                            {{ $evolucao->indicador === 'Piora' ? 'bg-red-50 border-red-200' : '' }}
                        ">
                            <div class="mt-1">
                                @if($evolucao->indicador === 'Melhora')
                                    <x-icon name="heroicon-o-arrow-trending-up" class="w-6 h-6 text-green-600" />
                                @elseif($evolucao->indicador === 'Estavel')
                                    <x-icon name="heroicon-o-minus" class="w-6 h-6 text-gray-600" />
                                @else
                                    <x-icon name="heroicon-o-arrow-trending-down" class="w-6 h-6 text-red-600" />
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $evolucao->indicador }} <span class="text-sm font-normal text-gray-500 ml-2">{{ \Carbon\Carbon::parse($evolucao->data_registro)->format('d/m/Y') }}</span></p>
                                <p class="text-gray-700 mt-2">{{ $evolucao->descricao }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">Nenhum registro de evolução.</p>
                    @endforelse
                </div>
            </div>

            <!-- ABA: Concluir -->
            <div x-show="tab === 'concluir'" style="display: none;" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Encerramento do Acompanhamento</h3>

                    @if($solicitacao->status === 'EmAtendimento' && Auth::user()->can('concluir', $solicitacao))
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <p class="text-yellow-700 text-sm">Ao concluir o acompanhamento, não será possível registrar novos atendimentos, planos de ação ou evoluções. O processo retornará ao Coordenador para ciência e será arquivado no prontuário do aluno.</p>
                        </div>

                        <form method="POST" action="{{ route('estudo-orientado.concluir', $solicitacao->id) }}">
                            @csrf
                            <div class="mb-4">
                                <x-input-label for="parecer_conclusao" :value="__('Parecer Final (Relatório de Encerramento)')" />
                                <textarea id="parecer_conclusao" name="parecer_conclusao" rows="6" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            </div>
                            <div class="flex justify-end">
                                <x-danger-button onclick="return confirm('Tem certeza que deseja encerrar este acompanhamento?')">
                                    Concluir e Encerrar Acompanhamento
                                </x-danger-button>
                            </div>
                        </form>
                    @elseif($solicitacao->status === 'Concluida')
                        <div class="bg-green-50 p-4 rounded-md border border-green-200">
                            <h4 class="font-bold text-green-800 mb-2">Acompanhamento Concluído</h4>
                            <p class="text-sm text-green-700 mb-4">Encerrado em {{ $solicitacao->data_conclusao->format('d/m/Y H:i') }} por {{ $solicitacao->concluidoPor->name ?? 'N/A' }}</p>
                            <div class="bg-white p-4 rounded border text-gray-700 whitespace-pre-wrap">{{ $solicitacao->parecer_conclusao }}</div>
                        </div>
                    @else
                        <p class="text-gray-500">O acompanhamento ainda não está em fase de atendimento, ou você não tem permissão para concluí-lo.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
