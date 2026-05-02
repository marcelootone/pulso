<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Lançamento de Notas') }}
            </h2>
            <a href="{{ route('avaliacoes.index', ['turma' => $turma->id, 'disciplina' => $avaliacao->disciplina]) }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-bold">
                &larr; Voltar para Avaliações
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6 rounded-r-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-indigo-800">{{ $avaliacao->nome }} ({{ $avaliacao->periodo }})</h3>
                        <p class="text-sm text-indigo-600 mt-1">Turma: {{ $turma->serie }}º {{ $turma->complemento }} | Disciplina: {{ $avaliacao->disciplina }}</p>
                    </div>
                    <div class="text-right">
                        <span class="block text-sm text-indigo-600 font-semibold uppercase tracking-wider">Valor Máximo</span>
                        <span class="text-3xl font-black text-indigo-700">{{ number_format($avaliacao->valor_maximo, 1, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded border border-green-200">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-200 p-3 rounded">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('notas.store', $avaliacao->id) }}" method="POST">
                        @csrf
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 mb-6 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">
                                            Aluno
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">
                                            Nota Obtida (Máx: {{ number_format($avaliacao->valor_maximo, 1, ',', '.') }})
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($alunos as $aluno)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $aluno->nome }}
                                                <span class="block text-xs text-gray-400 font-normal">RA: {{ $aluno->ra }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input 
                                                    type="number" 
                                                    name="notas[{{ $aluno->id }}]" 
                                                    value="{{ $notasLancadas[$aluno->id] ?? '' }}" 
                                                    step="0.1" 
                                                    min="0" 
                                                    max="{{ $avaliacao->valor_maximo }}" 
                                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-32 text-center text-lg font-bold"
                                                    placeholder="--">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-6 py-8 whitespace-nowrap text-center text-sm text-gray-500 italic">
                                                Nenhum aluno cadastrado nesta turma.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="px-8 py-3 text-lg">
                                {{ __('Salvar Notas') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
