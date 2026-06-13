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
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Turma</label>
                    <x-select name="turma_id" class="w-full" required onchange="this.form.submit()">
                        <option value="">Selecione a Turma...</option>
                        @foreach($turmas as $turma)
                            <option value="{{ $turma->id }}" {{ $turmaSelecionada == $turma->id ? 'selected' : '' }}>
                                {{ $turma->serie }}º {{ $turma->complemento }} - {{ $turma->modalidade }} ({{ $turma->turno }})
                            </option>
                        @endforeach
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
        @if($turmaSelecionada)
            <x-card>
                <form action="{{ route('frequencia.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="turma_id" value="{{ $turmaSelecionada }}">
                    <input type="hidden" name="data" value="{{ $dataSelecionada }}">

                    <div class="-mx-6 -my-6">
                        <x-table>
                            <x-slot name="head">
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Nº</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do Estudante</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status da Chamada</th>
                            </x-slot>
                            <x-slot name="body">
                                @forelse($alunos as $index => $aluno)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-center font-bold text-gray-400 text-sm">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 font-semibold text-gray-900 text-sm">{{ $aluno->nome }}</td>
                                        <td class="px-6 py-4">
                                            @hasanyrole('Secretaria|Coordenador')
                                                <div class="flex justify-start gap-2 flex-wrap">
                                                    @if(!$aluno->frequencia_lancada)
                                                        <span class="px-3 py-1.5 rounded-md bg-gray-100 text-gray-600 font-bold text-xs border border-gray-200 shadow-sm inline-block text-center">NÃO LANÇADA</span>
                                                    @else
                                                        @foreach($aluno->frequencias_dia as $freq)
                                                            @php $profNome = strtok($freq->user->name ?? 'Prof', ' '); @endphp
                                                            @if($freq->status == 'P')
                                                                <span class="px-3 py-1.5 rounded-md bg-green-100 text-green-800 font-bold text-xs border border-green-200 shadow-sm inline-block text-center" title="{{ $freq->user->name ?? '' }}">PRESENTE ({{ $profNome }})</span>
                                                            @elseif($freq->status == 'F')
                                                                <span class="px-3 py-1.5 rounded-md bg-red-100 text-red-800 font-bold text-xs border border-red-200 shadow-sm inline-block text-center" title="{{ $freq->user->name ?? '' }}">FALTA ({{ $profNome }})</span>
                                                            @elseif($freq->status == 'FJ')
                                                                <span class="px-3 py-1.5 rounded-md bg-yellow-100 text-yellow-800 font-bold text-xs border border-yellow-200 shadow-sm inline-block text-center" title="{{ $freq->user->name ?? '' }}">JUSTIFICADA ({{ $profNome }})</span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @else
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
                                            @endhasanyrole
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

                    @if($alunos->count() > 0)
                        @unless(auth()->user()->hasAnyRole(['Secretaria', 'Coordenador']))
                            <x-slot name="footer">
                                <div class="-mx-6 -my-4 bg-gray-50 px-6 py-4 border-t flex justify-end">
                                    <x-button variant="primary" type="submit">
                                        <x-heroicon-o-check class="w-5 h-5 mr-2" />
                                        Salvar Chamada
                                    </x-button>
                                </div>
                            </x-slot>
                        @endunless
                    @endif
                </form>
            </x-card>
        @else
            <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300 shadow-sm">
                <x-heroicon-o-calendar-days class="mx-auto h-12 w-12 text-gray-300 mb-3" />
                <h3 class="text-lg font-medium text-gray-900">Nenhuma turma selecionada</h3>
                <p class="mt-1 text-sm text-gray-500">Selecione uma turma e uma data acima para carregar a lista de alunos.</p>
            </div>
        @endif
    </div>
</x-app-layout>
