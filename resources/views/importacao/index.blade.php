<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Importar Estudantes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 shadow-sm">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Atenção:</strong> O cadastramento de <strong>ESTUDANTES</strong> em lote para a turma baseia-se no preenchimento da planilha modelo. Salve a mesma em <strong>formato CSV</strong> e carregue-a no botão abaixo.
                        </p>
                    </div>
                </div>
            </div>

            <!-- TABS -->
            <div class="flex mb-6 border-b border-gray-200">
                <a href="{{ route('importar.index', ['turma_id' => request('turma_id')]) }}" class="text-primary-600 border-b-2 border-primary-600 px-6 py-3 font-bold text-sm flex items-center transition-colors">
                    <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2" />
                    Importar Usuarios (Planilha)
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Ops! Algo deu errado:</p>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-gray-600">
                <form action="{{ route('importar.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Turma</label>
                            <select name="turma_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Selecione a turma...</option>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}" {{ (old('turma_id') ?? request('turma_id')) == $turma->id ? 'selected' : '' }}>
                                        {{ $turma->serie }}º {{ $turma->complemento }} - {{ $turma->modalidade }} ({{ $turma->turno }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Arquivo CSV</label>
                            <input type="file" name="planilha" accept=".csv, .xlsx, .xls" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-md p-1" required>
                        </div>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button type="submit" class="bg-green-600 text-black px-8 py-2 rounded-md hover:bg-green-700 font-bold shadow-md">
                            SALVAR
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="mt-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline text-sm">Caso você não esteja visualizando nenhuma turma, lembre-se que antes de realizar este procedimento é necessário o cadastramento das turmas.</span>
            </div>

        </div>
    </div>
</x-app-layout>