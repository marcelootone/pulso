<x-app-layout>
    <x-slot name="header">
        {{ __('Visualizar Chamada') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Pedagógico', 'url' => '#'],
            ['label' => 'Frequência', 'url' => route('frequencia.index')],
            ['label' => 'Monitorar']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">
        {{-- Navegação Secundária --}}
        <div class="mb-6 flex flex-wrap gap-3">
            <x-button variant="secondary" onclick="window.location='{{ route('frequencia.index') }}'">
                Visão Geral
            </x-button>
            <x-button variant="primary" onclick="window.location='{{ route('frequencia.monitorar') }}'">
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

        {{-- Formulário de Seleção de Turma e Data --}}
        <x-card class="mb-8 border-l-4 border-l-primary-500">
            <form method="GET" action="{{ route('frequencia.monitorar') }}" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Destino (Turma, Eletiva ou Clube)</label>
                    <x-select name="destino" class="w-full" required onchange="this.form.submit()">
                        <option value="">Selecione o destino...</option>

                        @if($turmas->count() > 0)
                            <optgroup label="Turmas Regulares">
                                @foreach($turmas as $turma)
                                    <option value="turma_{{ $turma->id }}" {{ $destinoSelecionado == 'turma_'.$turma->id ? 'selected' : '' }}>
                                        {{ $turma->serie }}º {{ $turma->complemento }} - {{ $turma->modalidade }} ({{ $turma->turno }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($eletivas->count() > 0)
                            <optgroup label="Eletivas">
                                @foreach($eletivas as $eletiva)
                                    <option value="eletiva_{{ $eletiva->id }}" {{ $destinoSelecionado == 'eletiva_'.$eletiva->id ? 'selected' : '' }}>
                                        {{ $eletiva->nome }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($clubes->count() > 0)
                            <optgroup label="Clubes">
                                @foreach($clubes as $clube)
                                    <option value="clube_{{ $clube->id }}" {{ $destinoSelecionado == 'clube_'.$clube->id ? 'selected' : '' }}>
                                        {{ $clube->nome }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </x-select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Data</label>
                    <x-input type="date" name="data" value="{{ $dataSelecionada }}" max="{{ date('Y-m-d') }}" required onchange="this.form.submit()" />
                </div>
                <noscript>
                    <x-button variant="primary" type="submit">Carregar</x-button>
                </noscript>
            </form>
        </x-card>

        {{-- Lista de Alunos para Lançamento --}}
        {{-- Lista de Disciplinas/Abas --}}
        @if($destinoSelecionado)
            @if(empty($disciplinas))
                <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300 shadow-sm">
                    <x-icon name="heroicon-o-document-text" class="mx-auto h-12 w-12 text-gray-300 mb-3" />
                    <h3 class="text-lg font-medium text-gray-900">Nenhuma disciplina registrada</h3>
                    <p class="mt-1 text-sm text-gray-500">Não há registros de aulas nesta data para a turma selecionada.</p>
                </div>
            @else
                <div x-data="{ activeTab: 0, showConfirmModal: false, formToSubmit: null }">
                    {{-- Tabs --}}
                    <div class="mb-4 flex space-x-2 border-b border-gray-200 overflow-x-auto">
                        @foreach($disciplinas as $index => $disciplina)
                            <button
                                @click="activeTab = {{ $index }}"
                                :class="{'border-primary-500 text-primary-600': activeTab === {{ $index }}, 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== {{ $index }}}"
                                class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                                {{ $disciplina->nome }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Tab Panels --}}
                    @foreach($disciplinas as $index => $disciplina)
                        <div x-show="activeTab === {{ $index }}" style="display: none;">
                            <x-card>
                                <form action="{{ route('frequencia.store') }}" method="POST" id="form-disciplina-{{ $index }}">
                                    @csrf
                                    <input type="hidden" name="destino" value="{{ $destinoSelecionado }}">
                                    <input type="hidden" name="data" value="{{ $dataSelecionada }}">
                                    <input type="hidden" name="professor_id" value="{{ $disciplina->professor_id }}">

                                    <div class="-mx-6 -my-6">
                                        <x-table>
                                            <x-slot name="head">
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Nº</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do Estudante</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status da Chamada</th>
                                            </x-slot>
                                            <x-slot name="body">
                                                @forelse($disciplina->alunos as $idx => $aluno)
                                                    <tr class="hover:bg-gray-50 transition-colors">
                                                        <td class="px-6 py-4 text-center font-bold text-gray-400 text-sm">{{ $idx + 1 }}</td>
                                                        <td class="px-6 py-4 font-semibold text-gray-900 text-sm">{{ $aluno->nome }}</td>
                                                        <td class="px-6 py-4">
                                                            <div class="flex justify-center gap-2 flex-wrap">
                                                                <label class="cursor-pointer">
                                                                    <input type="radio" name="frequencias[{{ $aluno->id }}]" value="P" class="peer sr-only" {{ $aluno->status_frequencia == 'P' ? 'checked' : '' }}>
                                                                    <span class="px-3 py-1.5 rounded-md bg-white border border-gray-300 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-700 font-bold text-xs transition-colors shadow-sm inline-block w-24 text-center">
                                                                        PRESENTE
                                                                    </span>
                                                                </label>

                                                                <label class="cursor-pointer">
                                                                    <input type="radio" name="frequencias[{{ $aluno->id }}]" value="F" class="peer sr-only" {{ $aluno->status_frequencia == 'F' ? 'checked' : '' }}>
                                                                    <span class="px-3 py-1.5 rounded-md bg-white border border-gray-300 peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-700 font-bold text-xs transition-colors shadow-sm inline-block w-24 text-center">
                                                                        FALTA
                                                                    </span>
                                                                </label>

                                                                <label class="cursor-pointer">
                                                                    <input type="radio" name="frequencias[{{ $aluno->id }}]" value="FJ" class="peer sr-only" {{ $aluno->status_frequencia == 'FJ' ? 'checked' : '' }}>
                                                                    <span class="px-3 py-1.5 rounded-md bg-white border border-gray-300 peer-checked:bg-yellow-500 peer-checked:text-white peer-checked:border-yellow-600 font-bold text-xs transition-colors shadow-sm inline-block w-28 text-center" title="Falta Justificada">
                                                                        JUSTIFICADA
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic font-medium">
                                                            Nenhum aluno ativo encontrado nesta turma.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </x-slot>
                                        </x-table>
                                    </div>

                                    @hasanyrole('Secretaria|Coordenador|Gestor')
                                        <x-slot name="footer">
                                            <div class="-mx-6 -my-4 bg-gray-50 px-6 py-4 border-t flex justify-end">
                                                <x-button variant="primary" type="button" @click="formToSubmit = 'form-disciplina-{{ $index }}'; showConfirmModal = true">
                                                    <x-icon name="heroicon-o-check" class="w-5 h-5 mr-2" />
                                                    Salvar Alteração
                                                </x-button>
                                            </div>
                                        </x-slot>
                                    @endhasanyrole
                                </form>
                            </x-card>
                        </div>
                    @endforeach

                    {{-- Modal de Confirmação --}}
                    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                            <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                            <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <x-icon name="heroicon-o-exclamation-triangle" class="h-6 w-6 text-yellow-600" />
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                Confirmar alteração de frequência
                                            </h3>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500">
                                                    Você está alterando uma frequência já registrada para esta disciplina e data.<br><br>
                                                    Deseja realmente continuar?
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="button" @click="document.getElementById(formToSubmit).submit()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Confirmar Alteração
                                    </button>
                                    <button type="button" @click="showConfirmModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300 shadow-sm">
                <x-icon name="heroicon-o-calendar"-days class="mx-auto h-12 w-12 text-gray-300 mb-3" />
                <h3 class="text-lg font-medium text-gray-900">Nenhuma turma selecionada</h3>
                <p class="mt-1 text-sm text-gray-500">Selecione uma turma e uma data acima para carregar as disciplinas com aulas registradas.</p>
            </div>
        @endif
    </div>
</x-app-layout>
