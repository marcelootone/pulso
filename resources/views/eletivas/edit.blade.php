<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Eletiva / Clube') }} - {{ $eletiva->nome }}
        </h2>
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('eletivas.update', $eletiva->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nome</label>
                                <input type="text" name="nome" value="{{ old('nome', $eletiva->nome) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select name="tipo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="eletiva" {{ old('tipo', $eletiva->tipo) == 'eletiva' ? 'selected' : '' }}>Eletiva</option>
                                    <option value="clube" {{ old('tipo', $eletiva->tipo) == 'clube' ? 'selected' : '' }}>Clube</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantidade de Vagas</label>
                                <input type="text" inputmode="numeric" pattern="\d+" name="vagas" value="{{ old('vagas', $eletiva->vagas) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ano Letivo</label>
                                <input type="text" inputmode="numeric" pattern="\d+" maxlength="4" name="ano_letivo" value="{{ old('ano_letivo', $eletiva->ano_letivo) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Descrição</label>
                            <textarea name="descricao" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('descricao', $eletiva->descricao) }}</textarea>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Professores Responsáveis</label>
                            @php
                                $professoresAtuais = $eletiva->professores->pluck('id')->toArray();
                            @endphp
                            <select id="professor_ids" name="professor_ids[]" multiple required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Selecione --</option>
                                @foreach($professores as $prof)
                                    <option value="{{ $prof->id }}" {{ in_array($prof->id, old('professor_ids', $professoresAtuais)) ? 'selected' : '' }}>
                                        {{ $prof->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="usa_nota" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm" {{ old('usa_nota', $eletiva->usa_nota) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm font-medium text-gray-700">Utiliza sistema de notas (Professor poderá lançar notas)</span>
                            </label>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('eletivas.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancelar</a>
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">Atualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
