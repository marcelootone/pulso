<x-app-layout>
    <x-slot name="header">
        {{ __('Editar Eletiva / Clube') }} - {{ $eletiva->nome }}
    </x-slot>

    <!-- TomSelect CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Eletivas', 'url' => route('eletivas.index')],
            ['label' => 'Editar']
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <x-card class="border-t-4 border-t-amber-500">
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <x-icon name="heroicon-o-pencil-square" class="w-5 h-5 mr-2 text-amber-500" />
                        Atualizar Dados
                    </h3>
                    <a href="{{ route('eletivas.index') }}" class="text-sm font-medium text-amber-600 hover:text-amber-800">
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

            <form id="form-960dfb" action="{{ route('eletivas.update', $eletiva->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Nome</label>
                            <x-input type="text" name="nome" value="{{ old('nome', $eletiva->nome) }}" required class="w-full" />
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Tipo</label>
                            <x-select name="tipo" required class="w-full">
                                <option value="eletiva" {{ old('tipo', $eletiva->tipo) == 'eletiva' ? 'selected' : '' }}>Eletiva</option>
                                <option value="clube" {{ old('tipo', $eletiva->tipo) == 'clube' ? 'selected' : '' }}>Clube</option>
                            </x-select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Quantidade de Vagas</label>
                            <x-input type="text" inputmode="numeric" pattern="\d+" name="vagas" value="{{ old('vagas', $eletiva->vagas) }}" required class="w-full" />
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Ano Letivo</label>
                            <x-input type="text" inputmode="numeric" pattern="\d+" maxlength="4" name="ano_letivo" value="{{ old('ano_letivo', $eletiva->ano_letivo) }}" required class="w-full" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Descrição</label>
                        <textarea name="descricao" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm">{{ old('descricao', $eletiva->descricao) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Professores Responsáveis</label>
                        @php
                            $professoresAtuais = $eletiva->professores->pluck('id')->toArray();
                        @endphp
                        <select id="professor_ids" name="professor_ids[]" multiple required class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Selecione --</option>
                            @foreach($professores as $prof)
                                <option value="{{ $prof->id }}" {{ in_array($prof->id, old('professor_ids', $professoresAtuais)) ? 'selected' : '' }}>
                                    {{ $prof->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="usa_nota" value="1" class="rounded border-amber-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50 w-5 h-5 cursor-pointer" {{ old('usa_nota', $eletiva->usa_nota) ? 'checked' : '' }}>
                            <span class="ml-3 text-sm font-bold text-amber-900">Utiliza sistema de notas (Professor poderá lançar notas)</span>
                        </label>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex items-center justify-end gap-3">
                        <x-button variant="secondary" type="button" onclick="window.location='{{ route('eletivas.index') }}'">Cancelar</x-button>
                        <x-button variant="primary" class="!bg-amber-500 hover:!bg-amber-600 focus:!ring-amber-500 border-none" type="submit" form="form-960dfb">
                            <x-icon name="heroicon-o-check" class="w-4 h-4 mr-2" /> Atualizar
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
