<x-app-layout>
    <x-slot name="header">
        {{ __('Vincular Aluno a Turma') }}
    </x-slot>

    <!-- TomSelect CSS para Selects com busca -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Acadêmico', 'url' => '#'],
            ['label' => 'Turmas', 'url' => route('turmas.index')],
            ['label' => 'Vincular Aluno']
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        @if (session('success'))
            <div class="mb-6">
                <x-alert type="success" message="{{ session('success') }}" />
            </div>
        @endif

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
                    <h3 class="text-lg font-bold text-gray-900">Selecione o Aluno e a Turma</h3>
                    <a href="{{ request('turma_id') ? route('turmas.show', request('turma_id')) : route('turmas.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                        Voltar
                    </a>
                </div>
            </x-slot>

            <form method="POST" action="{{ route('vinculo.store') }}">
                @csrf

                <div class="space-y-6">
                    <!-- Aluno -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2" for="aluno_id">Selecione o Aluno</label>
                        <select id="aluno_id" name="aluno_id" class="w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" required>
                            <option value="">-- Selecione --</option>
                            @foreach($alunos as $aluno)
                                <option value="{{ $aluno->id }}" {{ old('aluno_id') == $aluno->id ? 'selected' : '' }}>
                                    {{ $aluno->nome }} (RA: {{ $aluno->ra }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Turma -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2" for="turma_id">Selecione a Turma</label>
                        <select id="turma_id" name="turma_id" class="w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" required>
                            <option value="">-- Selecione --</option>
                            @foreach($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ (old('turma_id') ?? request('turma_id')) == $turma->id ? 'selected' : '' }}>
                                    {{ $turma->serie }}º {{ $turma->complemento }} - {{ $turma->turno }} ({{ $turma->ano_letivo }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo de Vínculo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2" for="tipo_vinculo">Tipo de Vínculo</label>
                        <x-select id="tipo_vinculo" name="tipo_vinculo" required class="w-full">
                            <option value="REGULAR" {{ old('tipo_vinculo') == 'REGULAR' ? 'selected' : '' }}>Regular (Principal)</option>
                            <option value="ELETIVA" {{ old('tipo_vinculo') == 'ELETIVA' ? 'selected' : '' }}>Eletiva</option>
                            <option value="ITINERARIO" {{ old('tipo_vinculo') == 'ITINERARIO' ? 'selected' : '' }}>Itinerário Formativo</option>
                            <option value="REFORCO" {{ old('tipo_vinculo') == 'REFORCO' ? 'selected' : '' }}>Reforço</option>
                            <option value="AEE" {{ old('tipo_vinculo') == 'AEE' ? 'selected' : '' }}>Atendimento Educacional Especializado (AEE)</option>
                            <option value="DEPENDENCIA" {{ old('tipo_vinculo') == 'DEPENDENCIA' ? 'selected' : '' }}>Dependência</option>
                        </x-select>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex items-center justify-end gap-3">
                        <x-button variant="secondary" type="button" onclick="window.history.back()">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit">
                            <x-heroicon-o-link class="w-4 h-4 mr-2" />
                            Vincular Aluno
                        </x-button>
                    </div>
                </x-slot>
            </form>
        </x-card>
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
