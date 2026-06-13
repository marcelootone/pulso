<x-app-layout>
    <x-slot name="header">
        Detalhes da Turma: {{ $turma->serie }}º {{ $turma->complemento }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Acadêmico', 'url' => '#'],
            ['label' => 'Turmas', 'url' => route('turmas.index')],
            ['label' => $turma->serie . 'º ' . $turma->complemento]
        ]" />
    </x-slot>

    <x-slot name="actions">
        @can('gerenciar turmas')
            <div class="flex gap-2">
                <x-button variant="primary" onclick="window.location='{{ route('importar.index') }}?turma_id={{ $turma->id }}'">
                    <x-heroicon-o-user-plus class="w-4 h-4 mr-2" />
                    Matricular Aluno
                </x-button>
            </div>
        @endhasrole
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-card class="border-l-4 border-l-primary-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary-100 text-primary-600 mr-4">
                    <x-heroicon-o-users class="w-8 h-8" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Estudantes Matriculados</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $turma->enturmacoes->count() }}</p>
                </div>
            </div>
        </x-card>
        
        <x-card class="border-l-4 border-l-purple-500 md:col-span-2">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <x-heroicon-o-information-circle class="w-8 h-8" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Informações da Turma</p>
                    <p class="text-base text-gray-900 mt-1">
                        <strong>Turno:</strong> {{ $turma->turno }} &bull; 
                        <strong>Ano Letivo:</strong> {{ $turma->ano_letivo }} &bull; 
                        <strong>Status:</strong> 
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $turma->ativa ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $turma->ativa ? 'Ativa' : 'Inativa' }}
                        </span>
                    </p>
                </div>
            </div>
        </x-card>
    </div>

    <x-card class="mb-8">
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Equipe Docente (Professores Vinculados)</h3>
                @can('gerenciar turmas')
                    <a href="{{ url('atribuir-aulas') }}?turma_id={{ $turma->id }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                        + Atribuir Aula
                    </a>
                @endhasrole
            </div>
        </x-slot>

        <div class="-mx-6 -my-6">
            <x-table>
                <x-slot name="head">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professor(a)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disciplina</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($turma->professores as $professor)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold mr-3">
                                    {{ substr($professor->name, 0, 1) }}
                                </div>
                                {{ $professor->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ $professor->pivot->disciplina }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @can('gerenciar turmas')
                                    <form action="{{ route('atribuicoes.destroy', ['turma' => $turma->id, 'professor' => $professor->id]) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja desvincular este professor da disciplina de {{ $professor->pivot->disciplina }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="disciplina" value="{{ $professor->pivot->disciplina }}">
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">Remover</button>
                                    </form>
                                @endhasrole
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">
                                Nenhum professor atribuído a esta turma ainda.
                            </td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-table>
        </div>
    </x-card>

    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-bold text-gray-900">Lista de Chamada Oficial</h3>
        </x-slot>

        <div class="-mx-6 -my-6">
            <x-table>
                <x-slot name="head">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Nº</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RA</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do Estudante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vínculo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($turma->enturmacoes as $index => $enturmacao)
                        @php $aluno = $enturmacao->matricula->aluno; @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $aluno->ra }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $aluno->nome }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $enturmacao->tipo_vinculo }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @can('gerenciar estudantes')
                                    <div class="flex justify-end items-center gap-3">
                                        <a href="{{ route('alunos.edit', $aluno->id) }}" class="text-primary-600 hover:text-primary-900">Editar</a>
                                        <form action="{{ route('alunos.destroy', $aluno->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover este aluno desta turma? Ele ainda continuará no sistema.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Remover</button>
                                        </form>
                                    </div>
                                @endhasrole
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                Nenhum estudante matriculado nesta turma.
                            </td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-table>
        </div>
    </x-card>
</x-app-layout>