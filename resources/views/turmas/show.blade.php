<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalhes da Turma: {{ $turma->serie }}º {{ $turma->complemento }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow mb-6 flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 uppercase font-bold">Total de Estudantes Matriculados</p>
                <p class="text-3xl font-black text-indigo-600">{{ $turma->enturmacoes->count() }}</p>
            </div>
            <a href="{{ route('turmas.index') }}" class="text-gray-600 font-bold hover:underline">⬅ Voltar para Turmas</a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-lg mb-4 border-b pb-2">Lista de Chamada Oficial</h3>
            
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase">
                        <th class="p-3">Nº</th>
                        <th class="p-3">RA</th>
                        <th class="p-3">Nome do Estudante</th>
                        <th class="p-3">Vínculo</th>
                        <th class="p-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($turma->enturmacoes as $index => $enturmacao)
                        @php $aluno = $enturmacao->matricula->aluno; @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 font-bold text-gray-500">{{ $index + 1 }}</td>
                            <td class="p-3">{{ $aluno->ra }}</td>
                            <td class="p-3 font-medium text-gray-800">{{ $aluno->nome }}</td>
                            <td class="p-3 text-sm text-gray-600">{{ $enturmacao->tipo_vinculo }}</td>
                            <td class="p-3 text-center flex justify-center gap-2">
                                <a href="{{ route('alunos.edit', $aluno->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md text-sm font-semibold transition">✏️ Editar</a>
                                <form action="{{ route('alunos.destroy', $aluno->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este aluno desta turma? Ele ainda continuará no sistema.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-sm font-semibold transition">🗑️ Remover</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500 italic">Nenhum estudante matriculado nesta turma.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>