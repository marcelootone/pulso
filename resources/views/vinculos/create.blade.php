<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vincular Aluno a Turma') }}
        </h2>
        <!-- TomSelect CSS para Selects com busca -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-4 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('vinculo.store') }}" class="space-y-6">
                        @csrf

                        <!-- Aluno -->
                        <div>
                            <x-input-label for="aluno_id" :value="__('Selecione o Aluno')" />
                            <select id="aluno_id" name="aluno_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Selecione --</option>
                                @foreach($alunos as $aluno)
                                    <option value="{{ $aluno->id }}" {{ old('aluno_id') == $aluno->id ? 'selected' : '' }}>
                                        {{ $aluno->nome }} (RA: {{ $aluno->ra }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('aluno_id')" class="mt-2" />
                        </div>

                        <!-- Turma -->
                        <div>
                            <x-input-label for="turma_id" :value="__('Selecione a Turma')" />
                            <select id="turma_id" name="turma_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Selecione --</option>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                                        {{ $turma->serie }} {{ $turma->complemento }} - {{ $turma->turno }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('turma_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Vincular Aluno') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TomSelect JS e Inicialização -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new TomSelect("#aluno_id", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "-- Selecione o Aluno --",
                maxOptions: 50 // Otimiza a renderização de muitos itens
            });

            new TomSelect("#turma_id", {
                create: false,
                placeholder: "-- Selecione a Turma --"
            });
        });
    </script>
</x-app-layout>
