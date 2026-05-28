<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nova Turma') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-red-600">
                
                <form action="{{ route('turmas.store') }}" method="POST">
                    @csrf <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">MODALIDADE</label>
                        <select name="modalidade" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                            <option value="">Selecione...</option>
                            <optgroup label="INFANTIL">
                                <option value="EI - Educação Infantil">EI - Educação Infantil</option>
                            </optgroup>
                            <optgroup label="REGULAR">
                                <option value="EF - Ensino Fundamental">EF - Ensino Fundamental</option>
                                <option value="EM - Ensino Médio">EM - Ensino Médio</option>
                                <option value="EMI - Ensino Médio Integrado">EMI - Ensino Médio Integrado</option>
                            </optgroup>
                            <optgroup label="EJA">
                                <option value="EJA EF - Ensino de Jovens e Adultos Fundamental">EJA EF</option>
                                <option value="EJA EM - Ensino de Jovens e Adultos Médio">EJA EM</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">TURNO</label>
                            <select name="turno" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="Matutino">Matutino</option>
                                <option value="Vespertino">Vespertino</option>
                                <option value="Noturno">Noturno</option>
                                <option value="Integral">Integral</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">SÉRIE</label>
                            <input type="number" name="serie" min="1" max="9" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">COMPLEMENTO</label>
                            <input type="text" name="complemento" maxlength="3" placeholder="Ex: A" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm uppercase">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">ANO LETIVO</label>
                            <input type="number" name="ano_letivo" min="2000" max="2099" value="{{ date('Y') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-red-600 text-black px-6 py-2 rounded-md hover:bg-red-700 font-bold">
                            CRIAR
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>