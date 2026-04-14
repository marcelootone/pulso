<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Chamada: {{ $turma->serie }}º {{ $turma->complemento }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('diario.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="turma_id" value="{{ $turma->id }}">

                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase">Data da Aula</label>
                            <input type="date" id="input_data" name="data" value="{{ $dataSelecionada }}" 
                                   onchange="window.location.href='/meu-diario/{{ $turma->id }}?data=' + this.value"
                                   class="rounded-md border-gray-300 shadow-sm focus:border-blue-500">
                        </div>
                        <button type="submit" class="bg-green-700 text-black px-6 py-2 rounded-md font-bold hover:bg-green-800 shadow-sm">
                            SALVAR CHAMADA
                        </button>
                    </div>

                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                                <th class="p-3">RA</th>
                                <th class="p-3">Estudante</th>
                                <th class="p-3 text-center">Frequência</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($turma->alunos as $aluno)
                            @php
                                // Verifica se já existe um registro para este aluno hoje
                                $registro = $frequenciasExistentes[$aluno->id] ?? null;
                                $statusAtual = $registro ? $registro->status : 'P';
                            @endphp
                            <tr class="border-b hover:bg-gray-50 transition-colors">
                                <td class="p-3 text-sm font-mono text-gray-400">{{ $aluno->ra }}</td>
                                <td class="p-3 font-semibold text-gray-700">{{ $aluno->nome }}</td>
                                <td class="p-3">
                                    <div class="flex justify-center gap-6">
                                        <label class="flex items-center space-x-2 cursor-pointer group">
                                            <input type="radio" name="presencas[{{ $aluno->id }}]" value="P" 
                                                   {{ $statusAtual == 'P' ? 'checked' : '' }}
                                                   class="text-green-600 focus:ring-green-500">
                                            <span class="text-sm font-bold text-gray-600 group-hover:text-green-600">P</span>
                                        </label>
                                        
                                        <label class="flex items-center space-x-2 cursor-pointer group">
                                            <input type="radio" name="presencas[{{ $aluno->id }}]" value="F" 
                                                   {{ $statusAtual == 'F' ? 'checked' : '' }}
                                                   class="text-red-600 focus:ring-red-500">
                                            <span class="text-sm font-bold text-gray-600 group-hover:text-red-600">F</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>