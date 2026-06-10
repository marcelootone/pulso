<x-app-layout>
    <x-slot name="header">
        {{ __('Nova Solicitação de Estudo Orientado') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Estudo Orientado', 'url' => route('estudo-orientado.solicitacoes.index')],
            ['label' => 'Nova Solicitação']
        ]" />
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <x-card class="border-t-4 border-t-indigo-600">
            <x-slot name="header">
                <div class="flex items-start">
                    <x-heroicon-o-document-text class="w-6 h-6 mr-3 text-indigo-600" />
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Solicitar Atividade</h3>
                        <p class="text-gray-500 text-sm mt-1">Descreva a atividade que deverá ser aplicada e acompanhada durante o horário de Estudo Orientado da turma.</p>
                    </div>
                </div>
            </x-slot>

            <form action="{{ route('estudo-orientado.solicitacoes.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    {{-- Turma --}}
                    <div>
                        <label for="turma_id" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Turma <span class="text-red-500">*</span>
                        </label>
                        <x-select id="turma_id" name="turma_id" required class="w-full {{ $errors->has('turma_id') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}">
                            <option value="">— Selecione a turma —</option>
                            @foreach($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                                    {{ $turma->serie }} {{ $turma->complemento }} — {{ $turma->turno }} ({{ $turma->modalidade }})
                                </option>
                            @endforeach
                        </x-select>
                        @error('turma_id')
                            <p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Disciplina --}}
                    <div>
                        <label for="disciplina_solicitante" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Sua Disciplina <span class="text-red-500">*</span>
                        </label>
                        <x-input type="text" id="disciplina_solicitante" name="disciplina_solicitante"
                            value="{{ old('disciplina_solicitante') }}"
                            placeholder="Ex: Matemática, Biologia, Português..."
                            required maxlength="100"
                            class="w-full {{ $errors->has('disciplina_solicitante') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" />
                        @error('disciplina_solicitante')
                            <p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Data Prevista --}}
                    <div>
                        <label for="data_prevista" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Data Prevista para Aplicação <span class="text-red-500">*</span>
                        </label>
                        <x-input type="date" id="data_prevista" name="data_prevista"
                            value="{{ old('data_prevista', date('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            required
                            class="w-full sm:w-64 {{ $errors->has('data_prevista') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" />
                        @error('data_prevista')
                            <p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descrição da Atividade --}}
                    <div>
                        <label for="descricao" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Descrição da Atividade <span class="text-red-500">*</span>
                        </label>
                        <textarea id="descricao" name="descricao" rows="6"
                            placeholder="Descreva detalhadamente o que os alunos deverão realizar. Ex: Resolver as questões 1 a 10 da pág. 45 do livro de Matemática, sobre frações..."
                            required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm {{ $errors->has('descricao') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}">{{ old('descricao') }}</textarea>
                        @error('descricao')
                            <p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 font-medium">Mínimo de 10 caracteres.</p>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex items-center justify-end gap-3">
                        <x-button variant="secondary" type="button" onclick="window.location='{{ route('estudo-orientado.solicitacoes.index') }}'">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit">
                            <x-heroicon-o-check class="w-5 h-5 mr-2" /> Criar Solicitação
                        </x-button>
                    </div>
                </x-slot>
            </form>
        </x-card>
    </div>
</x-app-layout>
