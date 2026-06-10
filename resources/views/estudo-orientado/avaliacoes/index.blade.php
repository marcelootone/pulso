<x-app-layout>
    <x-slot name="header">
        {{ __('Estudo Orientado — Atividades para Avaliar') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Estudo Orientado', 'url' => '#'],
            ['label' => 'Avaliações']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">

        @if (session('success'))
            <div class="mb-6">
                <x-alert type="success" message="{{ session('success') }}" />
            </div>
        @endif

        {{-- Filtro de status --}}
        <div class="mb-6 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center text-gray-700 font-bold">
                <x-heroicon-o-funnel class="w-5 h-5 mr-2 text-gray-400" />
                Filtros
            </div>
            <form action="{{ route('estudo-orientado.avaliacoes.index') }}" method="GET" class="flex gap-3">
                <x-select name="status" class="w-full sm:w-64 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                    <option value="">Todas (Pendentes + Avaliadas)</option>
                    <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Somente Pendentes</option>
                    <option value="Avaliada" {{ request('status') == 'Avaliada' ? 'selected' : '' }}>Somente Avaliadas</option>
                </x-select>
            </form>
        </div>

        {{-- Cards de Atividades --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($atividades as $atividade)
                <x-card class="hover:shadow-md transition-shadow h-full flex flex-col justify-between {{ $atividade->status === 'Pendente' ? 'border-t-4 border-t-amber-400' : 'border-t-4 border-t-green-500' }}">
                    <div class="flex-grow">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Badge de Status --}}
                            @if($atividade->status === 'Pendente')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                                    <x-heroicon-o-clock class="w-4 h-4 mr-1" /> Pendente
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-green-100 text-green-800">
                                    <x-heroicon-o-check-circle class="w-4 h-4 mr-1" /> Avaliada
                                </span>
                            @endif
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-md">Prev: {{ $atividade->data_prevista->format('d/m/Y') }}</span>
                        </div>

                        <h3 class="text-lg font-black text-gray-900 mb-1">
                            {{ $atividade->turma->serie ?? '—' }} {{ $atividade->turma->complemento ?? '' }}
                            <span class="font-bold text-gray-500 text-sm">— {{ $atividade->turma->turno ?? '' }}</span>
                        </h3>
                        <div class="text-sm text-gray-600 mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100 space-y-1">
                            <p>Solicitado por: <span class="font-bold text-gray-900">{{ $atividade->solicitante->name ?? '—' }}</span></p>
                            <p>Disciplina: <span class="font-bold text-indigo-600">{{ $atividade->disciplina_solicitante }}</span></p>
                        </div>
                        <p class="text-sm text-gray-700 font-medium leading-relaxed line-clamp-3 mb-4" title="{{ $atividade->descricao }}">{{ $atividade->descricao }}</p>

                        @if($atividade->status === 'Avaliada')
                            <div class="mt-auto mb-4 bg-green-50 rounded-lg p-3 border border-green-100">
                                <p class="text-xs font-bold text-green-800 flex items-center justify-center">
                                    <x-heroicon-o-check-badge class="w-4 h-4 mr-1" />
                                    {{ $atividade->cumprimentos->where('cumpriu', true)->count() }} de {{ $atividade->cumprimentos->count() }} alunos cumpriram
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                        <x-button variant="{{ $atividade->status === 'Pendente' ? 'primary' : 'secondary' }}" 
                                  class="{{ $atividade->status === 'Pendente' ? 'w-full justify-center !bg-indigo-600 hover:!bg-indigo-700' : 'w-full justify-center' }}" 
                                  onclick="window.location='{{ route('estudo-orientado.avaliacoes.show', $atividade->id) }}'">
                            @if($atividade->status === 'Pendente')
                                <x-heroicon-o-play-circle class="w-5 h-5 mr-2" /> Avaliar Alunos
                            @else
                                <x-heroicon-o-eye class="w-5 h-5 mr-2" /> Ver Resultado
                            @endif
                        </x-button>
                    </div>
                </x-card>
            @empty
                <div class="col-span-1 md:col-span-2">
                    <x-card>
                        <div class="p-12 text-center border-2 border-dashed border-gray-200 rounded-lg bg-gray-50">
                            <x-heroicon-o-clipboard-document-check class="mx-auto mb-4 w-16 h-16 text-gray-300" />
                            <p class="text-gray-600 font-bold text-lg mb-2">Nenhuma atividade encontrada.</p>
                            @can('lancar avaliacoes')
                                <p class="text-gray-500 font-medium text-sm max-w-lg mx-auto">
                                    Você ainda não está vinculado a nenhuma turma, ou nenhum professor registrou uma atividade para as suas turmas.
                                    Solicite ao Gestor ou Secretaria que faça sua atribuição em
                                    <strong>Atribuir Aulas</strong>.
                                </p>
                            @endcan
                            @can('acompanhar rendimento')
                                <p class="text-gray-500 font-medium text-sm">Nenhum professor cadastrou solicitações de Estudo Orientado ainda.</p>
                            @endcan
                        </div>
                    </x-card>
                </div>
            @endforelse
        </div>

        @if($atividades->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $atividades->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
