<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nova Solicitação de Estudo Orientado
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Cabeçalho do card --}}
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-8 py-6">
                    <h3 class="text-black font-semibold text-lg">Solicitar Atividade ao Professor de Estudo Orientado</h3>
                    <p class="text-indigo-200 text-sm mt-1">Descreva a atividade que deverá ser aplicada e acompanhada durante o horário de Estudo Orientado da turma.</p>
                </div>

                <form action="{{ route('estudo-orientado.solicitacoes.store') }}" method="POST" class="p-8 space-y-6">
                    @csrf

                    {{-- Turma --}}
                    <div>
                        <label for="turma_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Turma <span class="text-red-500">*</span>
                        </label>
                        <select id="turma_id" name="turma_id" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('turma_id') border-red-500 @enderror">
                            <option value="">— Selecione a turma —</option>
                            @foreach($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                                    {{ $turma->serie }} {{ $turma->complemento }} — {{ $turma->turno }} ({{ $turma->modalidade }})
                                </option>
                            @endforeach
                        </select>
                        @error('turma_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Disciplina --}}
                    <div>
                        <label for="disciplina_solicitante" class="block text-sm font-medium text-gray-700 mb-1">
                            Sua Disciplina <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="disciplina_solicitante" name="disciplina_solicitante"
                            value="{{ old('disciplina_solicitante') }}"
                            placeholder="Ex: Matemática, Biologia, Português..."
                            required maxlength="100"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('disciplina_solicitante') border-red-500 @enderror">
                        @error('disciplina_solicitante')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Data Prevista --}}
                    <div>
                        <label for="data_prevista" class="block text-sm font-medium text-gray-700 mb-1">
                            Data Prevista para Aplicação <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="data_prevista" name="data_prevista"
                            value="{{ old('data_prevista', date('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('data_prevista') border-red-500 @enderror">
                        @error('data_prevista')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descrição da Atividade --}}
                    <div>
                        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">
                            Descrição da Atividade <span class="text-red-500">*</span>
                        </label>
                        <textarea id="descricao" name="descricao" rows="6"
                            placeholder="Descreva detalhadamente o que os alunos deverão realizar. Ex: Resolver as questões 1 a 10 da pág. 45 do livro de Matemática, sobre frações..."
                            required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('descricao') border-red-500 @enderror">{{ old('descricao') }}</textarea>
                        @error('descricao')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">Mínimo de 10 caracteres.</p>
                    </div>

                    {{-- Botões --}}
                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('estudo-orientado.solicitacoes.index') }}"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-5 rounded-lg transition">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-black font-semibold py-2 px-6 rounded-lg shadow transition">
                            Criar Solicitação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
