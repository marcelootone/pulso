<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Avaliações') }} - {{ $turma->serie }}º {{ $turma->complemento }} ({{ $disciplina }})
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if(session('success'))
            <div class="mb-6 font-medium text-sm text-green-600 bg-green-100 p-4 rounded-lg shadow">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-100 p-4 rounded-lg shadow">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulário de Criação -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 text-gray-900 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold mb-4 text-indigo-600">Nova Avaliação</h3>
                <form action="{{ route('avaliacoes.store', ['turma' => $turma->id, 'disciplina' => $disciplina]) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome (ex: Prova Mensal)</label>
                            <input type="text" name="nome" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Período (ex: 1º Bimestre)</label>
                            <input type="text" name="periodo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Valor Máximo</label>
                            <input type="number" name="valor_maximo" step="0.1" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data (Opcional)</label>
                            <input type="date" name="data" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <x-primary-button>Criar Avaliação</x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listagem de Avaliações -->
        <h3 class="text-xl font-bold mb-4 text-gray-800">Avaliações Cadastradas</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($avaliacoes as $avaliacao)
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 flex flex-col">
                    <div class="p-6 flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-lg font-bold text-gray-900">{{ $avaliacao->nome }}</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $avaliacao->periodo }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">
                            <strong>Valor:</strong> {{ number_format($avaliacao->valor_maximo, 1, ',', '.') }} pontos <br>
                            <strong>Data:</strong> {{ $avaliacao->data ? \Carbon\Carbon::parse($avaliacao->data)->format('d/m/Y') : 'Não definida' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                        <a href="{{ route('notas.create', $avaliacao->id) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Lançar Notas
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">
                    Nenhuma avaliação cadastrada para esta turma e disciplina ainda.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
