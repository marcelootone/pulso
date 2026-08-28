<x-app-layout>
    <x-slot name="header">
        Chamada: {{ $turma->serie }}º {{ $turma->complemento }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Pedagógico', 'url' => '#'],
            ['label' => 'Diário', 'url' => route('diario.index')],
            ['label' => 'Chamada']
        ]" />
    </x-slot>

    <div class="max-w-6xl mx-auto">
        <form action="{{ route('diario.store') }}" method="POST">
            @csrf
            <input type="hidden" name="turma_id" value="{{ $turma->id }}">

            <x-card class="mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="w-full sm:w-auto">
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Data da Aula</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-icon name="heroicon-o-calendar" class="h-5 w-5 text-gray-400" />
                            </div>
                            <x-input type="date" id="input_data" name="data" value="{{ $dataSelecionada }}"
                                   onchange="window.location.href='/meu-diario/{{ $turma->id }}?data=' + this.value"
                                   class="pl-10 font-bold text-primary-700 w-full sm:w-48" />
                        </div>
                    </div>

                    <div class="w-full sm:w-auto self-end">
                        <x-button variant="primary" type="submit" class="w-full sm:w-auto justify-center">
                            <x-icon name="heroicon-o-check" class="w-5 h-5 mr-2" />
                            Salvar Diário
                        </x-button>
                    </div>
                </div>
            </x-card>

            <!-- Seção: Conteúdo Ministrado -->
            <x-card class="mb-6 border-l-4 border-l-purple-500">
                <x-slot name="header">
                    <h3 class="font-bold text-lg text-gray-900 flex items-center">
                        <x-icon name="heroicon-o-document-text" class="w-5 h-5 mr-2 text-purple-600" />
                        Conteúdo Ministrado
                    </h3>
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-purple-700 uppercase tracking-wider mb-2">1ª Aula</label>
                        <textarea name="conteudos[1]" rows="3" placeholder="Ex: Álgebra e Equações..."
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ $conteudosExistentes['1']->descricao ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-purple-700 uppercase tracking-wider mb-2">2ª Aula</label>
                        <textarea name="conteudos[2]" rows="3" placeholder="Ex: Resolução de Exercícios..."
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ $conteudosExistentes['2']->descricao ?? '' }}</textarea>
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <h3 class="font-bold text-lg text-gray-900 flex items-center">
                        <x-icon name="heroicon-o-users" class="w-5 h-5 mr-2 text-gray-600" />
                        Lista de Presença
                    </h3>
                </x-slot>

                <div class="-mx-6 -my-6">
                    <x-table>
                        <x-slot name="head">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">RA</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudante</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Frequência</th>
                        </x-slot>
                        <x-slot name="body">
                            @forelse($turma->enturmacoes as $enturmacao)
                                @php
                                    $aluno = $enturmacao->matricula->aluno;
                                    $registro = $frequenciasExistentes[$aluno->id] ?? null;
                                    $statusAtual = $registro ? $registro->status : 'P';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-400">{{ $aluno->ra }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $aluno->nome }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex justify-center gap-6">
                                            <label class="flex items-center space-x-2 cursor-pointer group">
                                                <input type="radio" name="presencas[{{ $aluno->id }}]" value="P"
                                                       {{ $statusAtual == 'P' ? 'checked' : '' }}
                                                       class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                                                <span class="text-sm font-bold {{ $statusAtual == 'P' ? 'text-green-600' : 'text-gray-500 group-hover:text-green-600' }}">P</span>
                                            </label>

                                            <label class="flex items-center space-x-2 cursor-pointer group">
                                                <input type="radio" name="presencas[{{ $aluno->id }}]" value="F"
                                                       {{ $statusAtual == 'F' ? 'checked' : '' }}
                                                       class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300">
                                                <span class="text-sm font-bold {{ $statusAtual == 'F' ? 'text-red-600' : 'text-gray-500 group-hover:text-red-600' }}">F</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">
                                        Nenhum aluno enturmado nesta turma.
                                    </td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-table>
                </div>
            </x-card>
        </form>
    </div>
</x-app-layout>
