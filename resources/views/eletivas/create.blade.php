<x-app-layout>
    <x-slot name="header">
        {{ __('Nova Eletiva / Clube') }}
    </x-slot>

    <!-- TomSelect CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Eletivas', 'url' => route('eletivas.index')],
            ['label' => 'Nova']
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <x-card class="border-t-4 border-t-primary-600">
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Preencha os Dados</h3>
                    <a href="{{ route('eletivas.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                        Voltar
                    </a>
                </div>
            </x-slot>

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

            <form action="{{ route('eletivas.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Nome</label>
                            <x-input type="text" name="nome" value="{{ old('nome') }}" required class="w-full" />
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Tipo</label>
                            <x-select name="tipo" required class="w-full">
                                <option value="eletiva" {{ old('tipo') == 'eletiva' ? 'selected' : '' }}>Eletiva</option>
                                <option value="clube" {{ old('tipo') == 'clube' ? 'selected' : '' }}>Clube</option>
                            </x-select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Quantidade de Vagas</label>
                            <x-input type="text" inputmode="numeric" pattern="\d+" name="vagas" value="{{ old('vagas') }}" required class="w-full" />
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Ano Letivo</label>
                            <x-input type="text" inputmode="numeric" pattern="\d+" maxlength="4" name="ano_letivo" value="{{ old('ano_letivo', date('Y')) }}" required class="w-full" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Descrição</label>
                        <textarea name="descricao" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ old('descricao') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Professores Responsáveis</label>
                        <select id="professor_ids" name="professor_ids[]" multiple required class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Selecione --</option>
                            @foreach($professores as $prof)
                                <option value="{{ $prof->id }}" {{ in_array($prof->id, old('professor_ids', [])) ? 'selected' : '' }}>
                                    {{ $prof->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="usa_nota" value="1" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 w-5 h-5 cursor-pointer" {{ old('usa_nota') ? 'checked' : '' }}>
                            <span class="ml-3 text-sm font-bold text-gray-700">Utiliza sistema de notas (Professor poderá lançar notas)</span>
                        </label>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex items-center justify-end gap-3">
                        <x-button variant="secondary" type="button" onclick="window.location='{{ route('eletivas.index') }}'">Cancelar</x-button>
                        <x-button variant="primary" type="submit">
                            <x-heroicon-o-check class="w-4 h-4 mr-2" /> Salvar Eletiva/Clube
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
            new TomSelect("#professor_ids", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "-- Selecione os Professores --",
                plugins: ['remove_button']
            });
        });
    </script>
</x-app-layout>
