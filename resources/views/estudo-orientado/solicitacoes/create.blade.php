<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nova Solicitação de Estudo Orientado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('estudo-orientado.solicitacoes.store') }}">
                        @csrf

                        <!-- Turma -->
                        <div class="mb-4">
                            <x-input-label for="turma_id" :value="__('Turma')" />
                            <select id="turma_id" name="turma_id" required onchange="buscarAlunos(this.value)"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" disabled selected>Selecione uma Turma</option>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                                        {{ $turma->serie }} {{ $turma->complemento ?? '' }} ({{ $turma->turno }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('turma_id')" class="mt-2" />
                        </div>

                        <!-- Aluno -->
                        <div class="mb-4">
                            <x-input-label for="aluno_id" :value="__('Aluno')" />
                            <select id="aluno_id" name="aluno_id" required disabled
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-gray-100">
                                <option value="" disabled selected>Selecione uma turma primeiro</option>
                            </select>
                            <x-input-error :messages="$errors->get('aluno_id')" class="mt-2" />
                        </div>

                        <!-- Disciplina -->
                        <div class="mb-4">
                            <x-input-label for="disciplina_solicitante" :value="__('Disciplina que Solicitou')" />
                            <x-text-input id="disciplina_solicitante" class="block mt-1 w-full" type="text" name="disciplina_solicitante" :value="old('disciplina_solicitante')" required />
                            <x-input-error :messages="$errors->get('disciplina_solicitante')" class="mt-2" />
                        </div>

                        <!-- Prioridade -->
                        <div class="mb-4">
                            <x-input-label for="prioridade" :value="__('Prioridade')" />
                            <select id="prioridade" name="prioridade" required
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Baixa" {{ old('prioridade') == 'Baixa' ? 'selected' : '' }}>Baixa</option>
                                <option value="Media" {{ old('prioridade', 'Media') == 'Media' ? 'selected' : '' }}>Média</option>
                                <option value="Alta" {{ old('prioridade') == 'Alta' ? 'selected' : '' }}>Alta</option>
                            </select>
                            <x-input-error :messages="$errors->get('prioridade')" class="mt-2" />
                        </div>

                        <!-- Motivo -->
                        <div class="mb-6">
                            <x-input-label for="motivo" :value="__('Motivo do Encaminhamento')" />
                            <p class="text-xs text-gray-500 mb-1">Descreva as dificuldades observadas, deficiências na aprendizagem ou razões para este encaminhamento pedagógico.</p>
                            <textarea id="motivo" name="motivo" rows="5" required
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('motivo') }}</textarea>
                            <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('estudo-orientado.solicitacoes.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Salvar Solicitação') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function buscarAlunos(turmaId) {
            const alunoSelect = document.getElementById('aluno_id');
            alunoSelect.innerHTML = '<option value="" disabled selected>Carregando...</option>';
            alunoSelect.disabled = true;
            alunoSelect.classList.add('bg-gray-100');

            if (!turmaId) return;

            fetch(`/estudo-orientado/api/turmas/${turmaId}/alunos`)
                .then(response => response.json())
                .then(data => {
                    alunoSelect.innerHTML = '<option value="" disabled selected>Selecione um Aluno</option>';
                    data.forEach(aluno => {
                        alunoSelect.innerHTML += `<option value="${aluno.id}">${aluno.nome}</option>`;
                    });
                    alunoSelect.disabled = false;
                    alunoSelect.classList.remove('bg-gray-100');
                    
                    // Se houver aluno_id no old() (em caso de erro de validação)
                    let oldAluno = "{{ old('aluno_id') }}";
                    if (oldAluno) {
                        alunoSelect.value = oldAluno;
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alunoSelect.innerHTML = '<option value="" disabled selected>Erro ao carregar alunos</option>';
                });
        }

        // Caso a turma já venha preenchida pelo old()
        document.addEventListener('DOMContentLoaded', function() {
            let selectedTurma = document.getElementById('turma_id').value;
            if (selectedTurma) {
                buscarAlunos(selectedTurma);
            }
        });
    </script>
    @endpush
</x-app-layout>
