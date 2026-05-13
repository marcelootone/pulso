<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Painel da Eletiva: {{ $eletiva->nome }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="col-span-1">
            <div class="bg-white p-6 rounded-lg shadow mb-6 border-t-4 border-green-500">
                <h3 class="font-bold text-lg mb-4 text-gray-800">Status das Vagas</h3>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Ocupadas: <strong class="text-xl">{{ $vagasOcupadas }}</strong></span>
                    <span class="text-gray-600">Restantes: <strong class="text-xl text-green-600">{{ $vagasRestantes }}</strong></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ ($vagasOcupadas / $eletiva->vagas) * 100 }}%"></div>
                </div>
            </div>

            @if($vagasRestantes > 0)
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                <h3 class="font-bold text-lg mb-4 text-gray-800">Nova Matrícula</h3>
                <form action="{{ route('eletivas.matricular', $eletiva->id) }}" method="POST">
                    @csrf
                    <label class="block text-sm font-bold text-gray-700 mb-2">Selecione o Estudante</label>
                    <select name="aluno_id" class="w-full rounded-md border-gray-300 mb-4 shadow-sm" required>
                        <option value="">Buscar aluno (Nome ou Turma original)...</option>
                        @foreach($alunosDisponiveis as $aluno)
                            <option value="{{ $aluno->id }}">
                                {{ $aluno->nome }} ({{ $aluno->turma->serie }}º {{ $aluno->turma->complemento }})
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-green-600 text-black px-4 py-2 rounded font-bold hover:bg-green-700 shadow">
                        + ADICIONAR À LISTA
                    </button>
                </form>
            </div>
            @else
            <div class="bg-red-50 p-6 rounded-lg shadow border border-red-200 text-center">
                <p class="text-red-600 font-bold text-lg">⚠️ Vagas Esgotadas</p>
                <p class="text-sm text-red-500">Remova um estudante para liberar espaço.</p>
            </div>
            @endif
        </div>

        <div class="col-span-2 bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Estudantes Matriculados</h3>
            
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs uppercase text-gray-500 border-b bg-gray-50">
                        <th class="p-3">Estudante</th>
                        <th class="p-3">Turma Origem</th>
                        <th class="p-3 text-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eletiva->alunos as $aluno)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $aluno->nome }}</td>
                        <td class="p-3 text-sm text-gray-600">{{ $aluno->turma->serie }}º {{ $aluno->turma->complemento }}</td>
                        <td class="p-3 text-right">
                            <form action="{{ route('eletivas.remover', ['id' => $eletiva->id, 'aluno' => $aluno->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-bold" onclick="return confirm('Tem certeza que deseja remover este estudante da eletiva?')">
                                    X REMOVER
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-6 text-center text-gray-500 italic">Nenhum estudante matriculado nesta eletiva ainda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>