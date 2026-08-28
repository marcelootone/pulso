<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center text-red-600">
            <x-icon name="heroicon-o-exclamation-triangle" class="w-6 h-6 mr-2" />
            {{ __('Busca Ativa (Alerta de Frequência)') }}
        </div>
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Pedagógico', 'url' => '#'],
            ['label' => 'Frequência', 'url' => route('frequencia.index')],
            ['label' => 'Busca Ativa']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">
        {{-- Navegação Secundária --}}
        <div class="mb-6 flex flex-wrap gap-3">
            <x-button variant="secondary" onclick="window.location='{{ route('frequencia.index') }}'">
                Visão Geral
            </x-button>
            <x-button variant="secondary" onclick="window.location='{{ route('frequencia.monitorar') }}'">
                Visualizar Chamada
            </x-button>
            <x-button variant="danger" onclick="window.location='{{ route('frequencia.busca_ativa') }}'">
                Busca Ativa (Faltas)
            </x-button>
        </div>

        @if(session('success'))
            <div class="mb-6">
                <x-alert type="success" message="{{ session('success') }}" />
            </div>
        @endif

        {{-- Filtro --}}
        <x-card class="mb-8 border-l-4 border-l-red-500">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Estudantes Infrequentes (Busca Ativa)</h3>
                    <p class="text-sm text-gray-500">Mês de referência: {{ $mes === 'todos' ? 'Todos' : str_pad($mes, 2, '0', STR_PAD_LEFT) }}/{{ $ano === 'todos' ? 'Todos' : $ano }}</p>
                </div>

                <form method="GET" action="{{ route('frequencia.busca_ativa') }}" class="flex flex-wrap items-end gap-4 w-full md:w-auto">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Mês</label>
                        <x-select name="mes" class="w-full md:w-28">
                            <option value="todos" {{ $mes === 'todos' ? 'selected' : '' }}>Todos</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $mes == $i && $mes !== 'todos' ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Ano</label>
                        <x-select name="ano" class="w-full md:w-28">
                            <option value="todos" {{ $ano === 'todos' ? 'selected' : '' }}>Todos</option>
                            @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                <option value="{{ $i }}" {{ $ano == $i && $ano !== 'todos' ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </x-select>
                    </div>
                    <div class="flex-grow md:flex-grow-0">
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Turma</label>
                        <x-select name="turma_id" class="w-full md:w-48">
                            <option value="">Todas as Turmas</option>
                            @foreach($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ $turmaId == $turma->id ? 'selected' : '' }}>
                                    {{ $turma->serie }}º {{ $turma->complemento }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="w-full md:w-auto mt-2 md:mt-0 pb-0">
                        <x-button variant="primary" type="submit" class="w-full justify-center md:w-auto">
                            <x-icon name="heroicon-o-funnel" class="w-5 h-5 mr-2" /> Filtrar
                        </x-button>
                    </div>
                </form>
            </div>
        </x-card>

        {{-- Lista de Alunos em Risco --}}
        <div class="space-y-6">
            @forelse($alunosRisco as $risco)
                <x-card class="border-t-4 border-t-red-500 overflow-hidden">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b pb-6 mb-6">
                        <div>
                            <h4 class="text-2xl font-black text-gray-900">{{ $risco->aluno->nome }}</h4>
                            <p class="text-sm font-medium text-gray-500 mt-1 flex items-center">
                                <x-icon name="heroicon-o-academic-cap" class="w-4 h-4 mr-1 text-gray-400" />
                                Turma: {{ $risco->turma->serie }}º {{ $risco->turma->complemento }}
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0 flex gap-4">
                            <div class="inline-flex flex-col items-end px-4 py-2 bg-yellow-50 rounded-xl border border-yellow-100">
                                <p class="text-xs uppercase font-bold text-yellow-800 tracking-wider">Faltas Anuais</p>
                                <p class="text-2xl font-black text-yellow-600 my-1">{{ number_format($risco->anual_percentual, 1) }}%</p>
                            </div>
                            <div class="inline-flex flex-col items-end px-5 py-3 bg-red-50 rounded-xl border border-red-100">
                                <p class="text-xs uppercase font-bold text-red-800 tracking-wider">Motivos do Alerta</p>
                                <p class="text-sm font-black text-red-600 my-1 max-w-xs text-right">{{ $risco->motivos }}</p>
                                <p class="text-xs font-semibold text-red-700">{{ $risco->total_faltas }} faltas injustificadas no mês</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {{-- Histórico de Contatos --}}
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <h5 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wider flex items-center">
                                <x-icon name="heroicon-o-clock" class="h-5 w-5 mr-2 text-gray-500" />
                                Histórico de Busca Ativa
                            </h5>

                            @if($risco->registros->count() > 0)
                                <div class="space-y-4 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($risco->registros as $registro)
                                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                            <div class="flex justify-between items-start mb-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ \Carbon\Carbon::parse($registro->data)->format('d/m/Y') }}
                                                </span>
                                                <span class="text-xs font-medium text-gray-500">
                                                    Por: {{ $registro->user->name }}
                                                </span>
                                            </div>
                                            <p class="text-gray-700 text-sm mt-2">"{{ $registro->observacao }}"</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <x-icon name="heroicon-o-document-magnifying-glass" class="w-10 h-10 mx-auto text-gray-300 mb-2" />
                                    <p class="text-sm text-gray-500">Nenhuma ação registrada neste ano letivo.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Formulário de Novo Contato --}}
                        <div>
                            <h5 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wider flex items-center">
                                <x-icon name="heroicon-o-plus"-circle class="h-5 w-5 mr-2 text-red-500" />
                                Registrar Nova Ação
                            </h5>
                            <form action="{{ route('frequencia.registrar_busca_ativa') }}" method="POST">
                                @csrf
                                <input type="hidden" name="aluno_id" value="{{ $risco->aluno->id }}">

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Data da Ação</label>
                                        <x-input type="date" name="data" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required class="w-full" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Observação / Medida Tomada <span class="text-red-500">*</span></label>
                                        <textarea name="observacao" rows="4" required placeholder="Ex: Entramos em contato com a família..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                                    </div>

                                    <x-button variant="danger" type="submit" class="w-full justify-center">
                                        <x-icon name="heroicon-o-document-plus" class="w-5 h-5 mr-2" />
                                        Salvar Registro
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </x-card>
            @empty
                <div class="bg-white rounded-xl shadow-sm border-2 border-dashed border-green-200 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                        <x-icon name="heroicon-o-check" class="h-8 w-8 text-green-600" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tudo em ordem!</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">Nenhum estudante atingiu os limiares de infrequência da SEDU (faltas injustificadas nas periodicidades semanal, mensal, trimestral ou anual) para os filtros selecionados.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
