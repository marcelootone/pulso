<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perfil do Estudante: ') }} {{ $aluno->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 border-t-4 border-indigo-600">
                
                <div class="mb-6 flex justify-between items-center border-b pb-4">
                    <h3 class="text-2xl font-black text-gray-800">Ficha do Aluno</h3>
                    <a href="{{ route('turmas.show', $aluno->turma_id) }}" class="text-sm text-indigo-600 hover:underline font-bold bg-indigo-50 px-3 py-2 rounded-md">⬅ Voltar para Turma</a>
                </div>

                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">Oops!</strong>
                        <span class="block sm:inline">Existem erros de preenchimento.</span>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('alunos.update', $aluno->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Seção: Dados Pessoais -->
                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-gray-700 mb-4 border-b-2 border-indigo-100 inline-block">1. Dados Pessoais</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NOME COMPLETO</label>
                                <input type="text" name="nome" value="{{ old('nome', $aluno->nome) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">DATA DE NASCIMENTO</label>
                                    <input type="text" name="nascimento" placeholder="DD/MM/AAAA" value="{{ old('nascimento', $aluno->nascimento) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">SEXO</label>
                                    <select name="sexo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Selecione...</option>
                                        <option value="M" {{ old('sexo', $aluno->sexo) == 'M' ? 'selected' : '' }}>Masculino (M)</option>
                                        <option value="F" {{ old('sexo', $aluno->sexo) == 'F' ? 'selected' : '' }}>Feminino (F)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Contato e Endereço -->
                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-gray-700 mb-4 border-b-2 border-indigo-100 inline-block">2. Contato e Endereço</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">TELEFONE DO ESTUDANTE</label>
                                <input type="text" name="telefone" value="{{ old('telefone', $aluno->telefone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">TELEFONE DO RESPONSÁVEL</label>
                                <input type="text" name="telefone_responsavel" value="{{ old('telefone_responsavel', $aluno->telefone_responsavel) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">NOME DA MÃE</label>
                            <input type="text" name="nome_mae" value="{{ old('nome_mae', $aluno->nome_mae) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">CEP</label>
                                <input type="text" name="cep" value="{{ old('cep', $aluno->cep) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">LOGRADOURO</label>
                                <input type="text" name="logradouro" value="{{ old('logradouro', $aluno->logradouro) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Dados Acadêmicos -->
                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-gray-700 mb-4 border-b-2 border-indigo-100 inline-block">3. Dados Acadêmicos</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">R.A. (Registro do Aluno)</label>
                                <input type="text" name="ra" value="{{ old('ra', $aluno->ra) }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly title="O RA não pode ser alterado diretamente por aqui.">
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-black text-indigo-700 uppercase">Status da Matrícula</label>
                                <p class="text-xs text-gray-500 mb-2">Defina o estado atual do aluno nesta turma.</p>
                                <select name="status_matricula" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold" required>
                                    <option value="Ativo" {{ old('status_matricula', $aluno->status_matricula) == 'Ativo' ? 'selected' : '' }}>🟢 Ativo</option>
                                    <option value="Novato" {{ old('status_matricula', $aluno->status_matricula) == 'Novato' ? 'selected' : '' }}>🔵 Novato</option>
                                    <option value="Transferido" {{ old('status_matricula', $aluno->status_matricula) == 'Transferido' ? 'selected' : '' }}>🟠 Transferido</option>
                                    <option value="Evasão" {{ old('status_matricula', $aluno->status_matricula) == 'Evasão' ? 'selected' : '' }}>🔴 Evasão</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Botão de Salvar -->
                    <div class="flex justify-end mt-6 pt-6 border-t">
                        <button type="submit" class="bg-indigo-600 text-black px-8 py-3 rounded-md hover:bg-indigo-700 font-black shadow-lg hover:shadow-xl transition duration-150 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            SALVAR ALTERAÇÕES
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
