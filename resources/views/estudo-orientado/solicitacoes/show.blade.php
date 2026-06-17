<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes do Encaminhamento - Professor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Informações Gerais</h3>
                        <p><strong>Aluno:</strong> {{ $solicitacao->aluno->nome }}</p>
                        <p><strong>Turma:</strong> {{ $solicitacao->turma->serie }} {{ $solicitacao->turma->complemento ?? '' }}</p>
                        <p><strong>Professor Solicitante:</strong> {{ $solicitacao->solicitante->name }} ({{ $solicitacao->disciplina_solicitante }})</p>
                        <p><strong>Data da Solicitação:</strong> {{ $solicitacao->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mt-2">
                            <strong>Status Atual:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $solicitacao->status === 'Pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $solicitacao->status === 'Aprovada' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $solicitacao->status === 'Rejeitada' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $solicitacao->status === 'EmAtendimento' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                {{ $solicitacao->status === 'Concluida' ? 'bg-green-100 text-green-800' : '' }}
                            ">
                                {{ $solicitacao->status }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Motivo / Justificativa</h3>
                        <p class="bg-gray-50 p-4 rounded-md border text-gray-700 whitespace-pre-wrap">{{ $solicitacao->motivo }}</p>
                        
                        @if($solicitacao->parecer_coordenador)
                            <h3 class="text-lg font-bold mt-4 mb-2 text-gray-700 border-b pb-2">Parecer da Coordenação</h3>
                            <p class="bg-blue-50 p-4 rounded-md border border-blue-100 text-gray-700 whitespace-pre-wrap">{{ $solicitacao->parecer_coordenador }}</p>
                        @endif

                        @if($solicitacao->parecer_conclusao)
                            <h3 class="text-lg font-bold mt-4 mb-2 text-gray-700 border-b pb-2">Relatório de Encerramento (Orientador)</h3>
                            <p class="bg-green-50 p-4 rounded-md border border-green-200 text-gray-700 whitespace-pre-wrap">{{ $solicitacao->parecer_conclusao }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-center pb-8 mt-6">
                <a href="{{ route('estudo-orientado.solicitacoes.index') }}" class="text-indigo-600 hover:underline">Voltar para a lista</a>
            </div>

        </div>
    </div>
</x-app-layout>
