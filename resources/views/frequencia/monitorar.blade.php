<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitorar Frequência da Turma') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Navegação Secundária --}}
            <div class="mb-6 flex space-x-4">
                <a href="{{ route('frequencia.index') }}" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 rounded-md font-bold shadow transition">Visão Geral</a>
                <a href="{{ route('frequencia.monitorar') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-bold shadow transition">Lançar Chamada</a>
                <a href="{{ route('frequencia.busca_ativa') }}" class="px-4 py-2 bg-white text-red-600 hover:bg-red-50 border border-gray-300 rounded-md font-bold shadow transition">Busca Ativa (Faltas)</a>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Sucesso!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Formulário de Seleção de Turma e Data --}}
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border-l-4 border-indigo-500">
                <form method="GET" action="{{ route('frequencia.monitorar') }}" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Turma</label>
                        <select name="turma_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm" required onchange="this.form.submit()">
                            <option value="">Selecione a Turma...</option>
                            @foreach($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ $turmaSelecionada == $turma->id ? 'selected' : '' }}>
                                    {{ $turma->serie }}º {{ $turma->complemento }} - {{ $turma->modalidade }} ({{ $turma->turno }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Data</label>
                        <input type="date" name="data" value="{{ $dataSelecionada }}" max="{{ date('Y-m-d') }}" class="rounded-md border-gray-300 shadow-sm text-sm" required onchange="this.form.submit()">
                    </div>
                    <noscript>
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded shadow font-bold text-sm">Carregar</button>
                    </noscript>
                </form>
            </div>

            {{-- Lista de Alunos para Lançamento --}}
            @if($turmaSelecionada)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form action="{{ route('frequencia.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="turma_id" value="{{ $turmaSelecionada }}">
                        <input type="hidden" name="data" value="{{ $dataSelecionada }}">

                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b-2 text-gray-600 text-sm uppercase">
                                    <th class="p-4 w-16 text-center">Nº</th>
                                    <th class="p-4">Nome do Estudante</th>
                                    <th class="p-4 text-center">Status da Chamada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alunos as $index => $aluno)
                                    <tr class="border-b hover:bg-gray-50 transition">
                                        <td class="p-4 text-center font-bold text-gray-500">{{ $index + 1 }}</td>
                                        <td class="p-4 font-semibold text-gray-800">{{ $aluno->nome }}</td>
                                        <td class="p-4">
                                            <div class="flex justify-center gap-2">
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="frequencias[{{ $aluno->id }}]" value="P" class="peer sr-only" {{ $aluno->status_frequencia == 'P' ? 'checked' : '' }}>
                                                    <span class="px-4 py-2 rounded bg-white border border-gray-300 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-700 font-bold text-sm transition">
                                                        PRESENTE
                                                    </span>
                                                </label>
                                                
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="frequencias[{{ $aluno->id }}]" value="F" class="peer sr-only" {{ $aluno->status_frequencia == 'F' ? 'checked' : '' }}>
                                                    <span class="px-4 py-2 rounded bg-white border border-gray-300 peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-700 font-bold text-sm transition">
                                                        FALTA
                                                    </span>
                                                </label>

                                                <label class="cursor-pointer">
                                                    <input type="radio" name="frequencias[{{ $aluno->id }}]" value="FJ" class="peer sr-only" {{ $aluno->status_frequencia == 'FJ' ? 'checked' : '' }}>
                                                    <span class="px-4 py-2 rounded bg-white border border-gray-300 peer-checked:bg-yellow-500 peer-checked:text-white peer-checked:border-yellow-600 font-bold text-sm transition" title="Falta Justificada">
                                                        JUSTIFICADA
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-8 text-center text-gray-400 italic font-semibold">
                                            Nenhum aluno ativo encontrado nesta turma.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if($alunos->count() > 0)
                            <div class="p-6 bg-gray-50 border-t flex justify-end">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded shadow flex items-center transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    SALVAR CHAMADA
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg border-2 border-dashed border-gray-300 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <p class="font-medium text-lg">Selecione uma turma acima para carregar a lista de alunos.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
