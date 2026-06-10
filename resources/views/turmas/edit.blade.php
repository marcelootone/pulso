<x-app-layout>
    <x-slot name="header">
        Editar Turma: {{ $turma->serie }}º {{ $turma->complemento }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Acadêmico', 'url' => '#'],
            ['label' => 'Turmas', 'url' => route('turmas.index')],
            ['label' => 'Editar']
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
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

        <x-card class="border-t-4 border-t-yellow-500">
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Atualize os dados da turma</h3>
                    <a href="{{ route('turmas.show', $turma->id) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                        Voltar para Detalhes
                    </a>
                </div>
            </x-slot>

            <form action="{{ route('turmas.update', $turma->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Modalidade</label>
                        <x-select name="modalidade" required class="w-full">
                            <option value="">Selecione...</option>
                            <optgroup label="INFANTIL">
                                <option value="EI - Educação Infantil" {{ old('modalidade', $turma->modalidade) === 'EI - Educação Infantil' ? 'selected' : '' }}>EI - Educação Infantil</option>
                            </optgroup>
                            <optgroup label="REGULAR">
                                <option value="EF - Ensino Fundamental" {{ old('modalidade', $turma->modalidade) === 'EF - Ensino Fundamental' ? 'selected' : '' }}>EF - Ensino Fundamental</option>
                                <option value="EM - Ensino Médio" {{ old('modalidade', $turma->modalidade) === 'EM - Ensino Médio' ? 'selected' : '' }}>EM - Ensino Médio</option>
                                <option value="EMI - Ensino Médio Integrado" {{ old('modalidade', $turma->modalidade) === 'EMI - Ensino Médio Integrado' ? 'selected' : '' }}>EMI - Ensino Médio Integrado</option>
                            </optgroup>
                            <optgroup label="EJA">
                                <option value="EJA EF - Ensino de Jovens e Adultos Fundamental" {{ old('modalidade', $turma->modalidade) === 'EJA EF - Ensino de Jovens e Adultos Fundamental' ? 'selected' : '' }}>EJA EF</option>
                                <option value="EJA EM - Ensino de Jovens e Adultos Médio" {{ old('modalidade', $turma->modalidade) === 'EJA EM - Ensino de Jovens e Adultos Médio' ? 'selected' : '' }}>EJA EM</option>
                            </optgroup>
                        </x-select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Turno</label>
                            <x-select name="turno" required class="w-full">
                                @foreach(['Matutino', 'Vespertino', 'Noturno', 'Integral'] as $turno)
                                    <option value="{{ $turno }}" {{ old('turno', $turma->turno) === $turno ? 'selected' : '' }}>{{ $turno }}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Série</label>
                            <x-input type="number" name="serie" min="1" max="9" value="{{ old('serie', $turma->serie) }}" required class="w-full" />
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Complemento</label>
                            <x-input type="text" name="complemento" maxlength="3" placeholder="Ex: COM" value="{{ old('complemento', $turma->complemento) }}" class="w-full uppercase" />
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Ano Letivo</label>
                            <x-input type="number" name="ano_letivo" min="2000" max="2099" value="{{ old('ano_letivo', $turma->ano_letivo) }}" required class="w-full" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-alert type="{{ $turma->ativa ? 'success' : 'error' }}" message="Status atual: {{ $turma->ativa ? 'Ativa' : 'Inativa' }}. Para {{ $turma->ativa ? 'desativar' : 'reativar' }} esta turma, use o botão correspondente na listagem de turmas." />
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <x-button variant="secondary" type="button" onclick="window.location='{{ route('turmas.show', $turma->id) }}'">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit">
                            <x-heroicon-o-check class="w-4 h-4 mr-2" /> Salvar Alterações
                        </x-button>
                    </div>
                </x-slot>
            </form>
        </x-card>
    </div>
</x-app-layout>
