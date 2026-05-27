<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Turma') }}: {{ $turma->serie }}º {{ $turma->complemento }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Atenção!</strong>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-indigo-600">

                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h3 class="text-lg font-bold text-gray-700">Dados da Turma</h3>
                    <a href="{{ route('turmas.index') }}" class="text-sm text-indigo-600 hover:underline font-bold bg-indigo-50 px-3 py-2 rounded-md">
                        ⬅ Voltar para Turmas
                    </a>
                </div>

                <form action="{{ route('turmas.update', $turma->id) }}" method="POST" id="form-editar-turma">
                    @csrf
                    @method('PUT')

                    {{-- Modalidade --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 uppercase">Modalidade</label>
                        <select name="modalidade" id="turma-modalidade"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                            <option value="">Selecione...</option>
                            <optgroup label="INFANTIL">
                                <option value="EI - Educação Infantil"
                                    {{ old('modalidade', $turma->modalidade) === 'EI - Educação Infantil' ? 'selected' : '' }}>
                                    EI - Educação Infantil
                                </option>
                            </optgroup>
                            <optgroup label="REGULAR">
                                <option value="EF - Ensino Fundamental"
                                    {{ old('modalidade', $turma->modalidade) === 'EF - Ensino Fundamental' ? 'selected' : '' }}>
                                    EF - Ensino Fundamental
                                </option>
                                <option value="EM - Ensino Médio"
                                    {{ old('modalidade', $turma->modalidade) === 'EM - Ensino Médio' ? 'selected' : '' }}>
                                    EM - Ensino Médio
                                </option>
                                <option value="EMI - Ensino Médio Integrado"
                                    {{ old('modalidade', $turma->modalidade) === 'EMI - Ensino Médio Integrado' ? 'selected' : '' }}>
                                    EMI - Ensino Médio Integrado
                                </option>
                            </optgroup>
                            <optgroup label="EJA">
                                <option value="EJA EF - Ensino de Jovens e Adultos Fundamental"
                                    {{ old('modalidade', $turma->modalidade) === 'EJA EF - Ensino de Jovens e Adultos Fundamental' ? 'selected' : '' }}>
                                    EJA EF
                                </option>
                                <option value="EJA EM - Ensino de Jovens e Adultos Médio"
                                    {{ old('modalidade', $turma->modalidade) === 'EJA EM - Ensino de Jovens e Adultos Médio' ? 'selected' : '' }}>
                                    EJA EM
                                </option>
                            </optgroup>
                        </select>
                    </div>

                    {{-- Turno / Série / Complemento --}}
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 uppercase">Turno</label>
                            <select name="turno" id="turma-turno"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                @foreach(['Matutino', 'Vespertino', 'Noturno', 'Integral'] as $turno)
                                    <option value="{{ $turno }}"
                                        {{ old('turno', $turma->turno) === $turno ? 'selected' : '' }}>
                                        {{ $turno }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 uppercase">Série</label>
                            <input type="number" name="serie" id="turma-serie"
                                   min="1" max="9"
                                   value="{{ old('serie', $turma->serie) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 uppercase">Complemento</label>
                            <input type="text" name="complemento" id="turma-complemento"
                                   maxlength="3"
                                   placeholder="Ex: COM"
                                   value="{{ old('complemento', $turma->complemento) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 uppercase">
                        </div>
                    </div>

                    {{-- Ano Letivo --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 uppercase">Ano Letivo</label>
                        <input type="number" name="ano_letivo" id="turma-ano-letivo"
                               min="2000" max="2099"
                               value="{{ old('ano_letivo', $turma->ano_letivo) }}"
                               class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Informativo: status de ativação --}}
                    <div class="mb-6 p-4 rounded-lg {{ $turma->ativa ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                        <p class="text-sm font-semibold {{ $turma->ativa ? 'text-green-700' : 'text-red-700' }}">
                            Status atual:
                            <span class="font-black">{{ $turma->ativa ? '🟢 Ativa' : '🔴 Inativa' }}</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Para {{ $turma->ativa ? 'desativar' : 'reativar' }} esta turma, use o botão correspondente na listagem de turmas.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('turmas.show', $turma->id) }}"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-6 py-2 rounded-md transition">
                            Cancelar
                        </a>
                        <button type="submit" id="btn-salvar-turma"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-8 py-2 rounded-md shadow transition flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            SALVAR ALTERAÇÕES
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
