<x-app-layout>
    <x-slot name="header">
        {{ __('Lançamento de Notas') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Pedagógico', 'url' => '#'],
            ['label' => 'Avaliações', 'url' => route('avaliacoes.index', ['turma' => $turma->id, 'disciplina' => $avaliacao->disciplina])],
            ['label' => 'Lançar Notas']
        ]" />
    </x-slot>

    <div class="max-w-5xl mx-auto">

        <x-card class="mb-8 border-l-4 border-l-purple-500 bg-purple-50">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-black text-purple-900">{{ $avaliacao->nome }} <span class="text-lg text-purple-600 font-bold ml-2">({{ $avaliacao->periodo }})</span></h3>
                    <p class="text-sm font-semibold text-purple-700 mt-1 flex items-center">
                        <x-icon name="heroicon-o-academic-cap" class="w-4 h-4 mr-1" /> Turma: {{ $turma->serie }}º {{ $turma->complemento }}
                        <span class="mx-2">&bull;</span>
                        <x-icon name="heroicon-o-book-open" class="w-4 h-4 mr-1" /> Disciplina: {{ $avaliacao->disciplina }}
                    </p>
                </div>
                <div class="text-left md:text-right bg-white px-4 py-2 rounded-lg shadow-sm border border-purple-100">
                    <span class="block text-xs text-purple-500 font-bold uppercase tracking-wider mb-1">Valor Máximo</span>
                    <span class="text-3xl font-black text-purple-700">{{ number_format($avaliacao->valor_maximo, 1, ',', '.') }}</span>
                </div>
            </div>
        </x-card>

        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <x-icon name="heroicon-o-users" class="w-5 h-5 mr-2 text-gray-500" />
                        Lista de Alunos
                    </h3>
                    <a href="{{ route('avaliacoes.index', ['turma' => $turma->id, 'disciplina' => $avaliacao->disciplina]) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                        &larr; Voltar
                    </a>
                </div>
            </x-slot>

            @if(session('success'))
                <div class="mb-6">
                    <x-alert type="success" message="{{ session('success') }}" />
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6">
                    <x-alert type="error">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                </div>
            @endif

            <form id="form-1eac94" action="{{ route('notas.store', $avaliacao->id) }}" method="POST">
                @csrf

                <div class="-mx-6 -my-6">
                    <x-table>
                        <x-slot name="head">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/3">Aluno</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">
                                Nota Obtida <span class="text-gray-400 lowercase font-normal">(Máx: {{ number_format($avaliacao->valor_maximo, 1, ',', '.') }})</span>
                            </th>
                        </x-slot>
                        <x-slot name="body">
                            @forelse($alunos as $aluno)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $aluno->nome }}</div>
                                        <div class="text-xs text-gray-500 mt-1 font-mono">RA: {{ $aluno->ra }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <x-input
                                            type="number"
                                            name="notas[{{ $aluno->id }}]"
                                            value="{{ $notasLancadas[$aluno->id] ?? '' }}"
                                            step="0.1"
                                            min="0"
                                            max="{{ $avaliacao->valor_maximo }}"
                                            class="w-24 text-center text-lg font-bold"
                                            placeholder="--" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                        Nenhum aluno cadastrado nesta turma.
                                    </td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-table>
                </div>

                @if($alunos->count() > 0)
                    <x-slot name="footer">
                        <div class="-mx-6 -my-4 bg-gray-50 px-6 py-4 border-t flex justify-end">
                            <x-button variant="primary" type="submit" form="form-1eac94">
                                <x-icon name="heroicon-o-check" class="w-5 h-5 mr-2" />
                                Salvar Notas
                            </x-button>
                        </div>
                    </x-slot>
                @endif
            </form>
        </x-card>
    </div>
</x-app-layout>
