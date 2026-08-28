<x-app-layout>
    <x-slot name="header">
        {{ __('Avaliações') }} - {{ $turma->serie }}º {{ $turma->complemento }} ({{ $disciplina }})
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Pedagógico', 'url' => '#'],
            ['label' => 'Minhas Turmas', 'url' => route('diario.index')],
            ['label' => 'Avaliações']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">

        @if(session('success'))
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

        <!-- Formulário de Criação -->
        <x-card class="mb-8 border-l-4 border-l-primary-500">
            <x-slot name="header">
                <h3 class="text-lg font-bold flex items-center text-gray-900">
                    <x-icon name="heroicon-o-plus-circle" class="w-5 h-5 mr-2 text-primary-600" />
                    Nova Avaliação
                </h3>
            </x-slot>

            <form action="{{ route('avaliacoes.store', ['turma' => $turma->id, 'disciplina' => $disciplina]) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Nome (ex: Prova Mensal)</label>
                        <x-input type="text" name="nome" required class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Período (ex: 1º Bimestre)</label>
                        <x-input type="text" name="periodo" required class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Valor Máximo</label>
                        <x-input type="number" name="valor_maximo" step="0.1" min="0" required class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Data (Opcional)</label>
                        <x-input type="date" name="data" class="w-full" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-button variant="primary" type="submit">
                        Criar Avaliação
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Listagem de Avaliações -->
        <h3 class="text-xl font-bold mb-6 text-gray-900">Avaliações Cadastradas</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($avaliacoes as $avaliacao)
                <x-card class="flex flex-col h-full border-t-4 border-t-purple-500">
                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-lg font-black text-gray-900">{{ $avaliacao->nome }}</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 uppercase tracking-wider">
                                {{ $avaliacao->periodo }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-6">
                            <div class="flex items-center text-sm">
                                <span class="w-24 font-bold text-gray-500 uppercase tracking-wider text-xs">Valor:</span>
                                <span class="font-bold text-gray-900">{{ number_format($avaliacao->valor_maximo, 1, ',', '.') }} pontos</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="w-24 font-bold text-gray-500 uppercase tracking-wider text-xs">Data:</span>
                                <span class="text-gray-700">{{ $avaliacao->data ? \Carbon\Carbon::parse($avaliacao->data)->format('d/m/Y') : 'Não definida' }}</span>
                            </div>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="-mx-6 -my-4 bg-gray-50 px-6 py-4 border-t">
                            <x-button variant="secondary" class="w-full justify-center" onclick="window.location='{{ route('notas.create', $avaliacao->id) }}'">
                                Lançar Notas
                            </x-button>
                        </div>
                    </x-slot>
                </x-card>
            @empty
                <div class="col-span-full bg-white p-12 rounded-xl shadow-sm text-center border border-dashed border-gray-300">
                    <x-icon name="heroicon-o-document-text" class="mx-auto h-12 w-12 text-gray-300 mb-3" />
                    <h3 class="text-lg font-medium text-gray-900">Nenhuma avaliação cadastrada</h3>
                    <p class="mt-1 text-sm text-gray-500">Crie a primeira avaliação utilizando o formulário acima.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
