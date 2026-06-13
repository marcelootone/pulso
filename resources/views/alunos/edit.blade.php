<x-app-layout>
    <x-slot name="header">
        Perfil do Estudante: {{ $aluno->nome }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Acadêmico', 'url' => '#'],
            ['label' => 'Alunos', 'url' => route('alunos.index')],
            ['label' => 'Editar Aluno']
        ]" />
    </x-slot>

    <div class="max-w-5xl mx-auto">
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

        <x-card class="border-t-4 border-t-primary-600">
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Ficha do Aluno</h3>
                    @if($turmaId)
                        <a href="{{ route('turmas.show', $turmaId) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                            ⬅ Voltar para Turma
                        </a>
                    @else
                        <a href="{{ route('alunos.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                            ⬅ Voltar para Alunos
                        </a>
                    @endif
                </div>
            </x-slot>

            <form id="form-bbb5d7" action="{{ route('alunos.update', $aluno->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-8">
                    <!-- Seção: Dados Pessoais -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">1. Dados Pessoais</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Nome Completo</label>
                                <x-input type="text" name="nome" value="{{ old('nome', $aluno->nome) }}" required class="w-full" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Data de Nascimento</label>
                                    <x-input type="text" name="nascimento" placeholder="DD/MM/AAAA" value="{{ old('nascimento', $aluno->nascimento) }}" class="w-full" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Sexo</label>
                                    <x-select name="sexo" class="w-full">
                                        <option value="">Selecione...</option>
                                        <option value="M" {{ old('sexo', $aluno->sexo) == 'M' ? 'selected' : '' }}>Masculino (M)</option>
                                        <option value="F" {{ old('sexo', $aluno->sexo) == 'F' ? 'selected' : '' }}>Feminino (F)</option>
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Contato e Endereço -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">2. Contato e Endereço</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Telefone do Estudante</label>
                                <x-input type="text" name="telefone" value="{{ old('telefone', $aluno->telefone) }}" class="w-full" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Telefone do Responsável</label>
                                <x-input type="text" name="telefone_responsavel" value="{{ old('telefone_responsavel', $aluno->telefone_responsavel) }}" class="w-full" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Nome da Mãe</label>
                            <x-input type="text" name="nome_mae" value="{{ old('nome_mae', $aluno->nome_mae) }}" class="w-full" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">CEP</label>
                                <x-input type="text" name="cep" value="{{ old('cep', $aluno->cep) }}" class="w-full" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Logradouro</label>
                                <x-input type="text" name="logradouro" value="{{ old('logradouro', $aluno->logradouro) }}" class="w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Dados Acadêmicos -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">3. Dados Acadêmicos</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">R.A. (Registro do Aluno)</label>
                                <x-input type="text" name="ra" value="{{ old('ra', $aluno->ra) }}" class="w-full bg-gray-100 cursor-not-allowed" readonly title="O RA não pode ser alterado diretamente por aqui." />
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-bold text-primary-700 uppercase tracking-wider mb-1">Status da Matrícula</label>
                                <p class="text-xs text-gray-500 mb-3">Defina o estado atual do aluno nesta turma.</p>
                                <x-select name="status_matricula" required class="w-full font-semibold">
                                    <option value="Ativo" {{ old('status_matricula', $aluno->status_matricula) == 'Ativo' ? 'selected' : '' }}>🟢 Ativo</option>
                                    <option value="Novato" {{ old('status_matricula', $aluno->status_matricula) == 'Novato' ? 'selected' : '' }}>🔵 Novato</option>
                                    <option value="Transferido" {{ old('status_matricula', $aluno->status_matricula) == 'Transferido' ? 'selected' : '' }}>🟠 Transferido</option>
                                    <option value="Evasão" {{ old('status_matricula', $aluno->status_matricula) == 'Evasão' ? 'selected' : '' }}>🔴 Evasão</option>
                                </x-select>
                            </div>
                        </div>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        @if($turmaId)
                            <x-button variant="secondary" type="button" onclick="window.location='{{ route('turmas.show', $turmaId) }}'">Cancelar</x-button>
                        @else
                            <x-button variant="secondary" type="button" onclick="window.location='{{ route('alunos.index') }}'">Cancelar</x-button>
                        @endif
                        <x-button variant="primary" type="submit" form="form-bbb5d7">
                            <x-heroicon-o-check class="w-4 h-4 mr-2" /> Salvar Alterações
                        </x-button>
                    </div>
                </x-slot>
            </form>
        </x-card>
    </div>
</x-app-layout>
