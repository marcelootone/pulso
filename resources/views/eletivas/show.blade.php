<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes') }} - {{ $eletiva->nome }} 
            <span class="ml-2 text-sm font-normal px-2 py-1 bg-gray-200 rounded">{{ ucfirst($eletiva->tipo) }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Resumo e Professores -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informações -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Informações</h3>
                    <p><strong>Descrição:</strong> {{ $eletiva->descricao ?? 'Nenhuma descrição informada.' }}</p>
                    <p class="mt-2"><strong>Vagas:</strong> {{ $eletiva->alunosAtivos->count() }} ocupadas de {{ $eletiva->vagas }} totais</p>
                    <p class="mt-2"><strong>Usa Nota:</strong> {{ $eletiva->usa_nota ? 'Sim' : 'Não' }}</p>
                    <p class="mt-2"><strong>Status:</strong> {{ $eletiva->ativa ? 'Ativa' : 'Inativa' }}</p>
                </div>

                <!-- Professores -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Professores Responsáveis</h3>
                    <ul class="list-disc pl-5">
                        @forelse($eletiva->professores as $prof)
                            <li class="mb-1">{{ $prof->name }}</li>
                        @empty
                            <li class="text-gray-500 italic">Nenhum professor vinculado.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Inscrição de Alunos (Apenas Gestor, Secretaria, Coordenador) -->
            @hasrole('Gestor|Secretaria|Coordenador')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Vincular Alunos</h3>
                @if($eletiva->alunosAtivos->count() >= $eletiva->vagas)
                    <div class="text-red-600 font-bold mb-4">Limite de vagas atingido. Não é possível inscrever mais alunos.</div>
                @else
                    <form action="{{ route('inscricao-eletiva.store', $eletiva->id) }}" method="POST">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4 items-end">
                            <div class="flex-grow">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Selecione os Alunos</label>
                                <select name="aluno_ids[]" multiple required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm h-32">
                                    @foreach($alunosParaInscricao as $aluno)
                                        <option value="{{ $aluno->id }}">{{ $aluno->nome }} (RA: {{ $aluno->ra }})</option>
                                    @endforeach
                                </select>
                                <small class="text-gray-500">Pressione CTRL para selecionar múltiplos alunos.</small>
                            </div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded h-10">
                                Inscrever Selecionados
                            </button>
                        </div>
                    </form>
                @endif
            </div>
            
            @if($eletiva->tipo === 'clube')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Troca de Clube (Transferência)</h3>
                <form action="{{ route('inscricao-eletiva.trocar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="clube_origem_id" value="{{ $eletiva->id }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Aluno a Transferir</label>
                            <select name="aluno_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Selecione...</option>
                                @foreach($eletiva->alunosAtivos as $aluno)
                                    <option value="{{ $aluno->id }}">{{ $aluno->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Clube de Destino</label>
                            <select name="clube_destino_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Selecione...</option>
                                @foreach(\App\Models\Eletiva::where('tipo', 'clube')->where('ativa', true)->where('id', '!=', $eletiva->id)->get() as $outroClube)
                                    <option value="{{ $outroClube->id }}">{{ $outroClube->nome }} ({{ $outroClube->alunosAtivos->count() }}/{{ $outroClube->vagas }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-black font-bold py-2 px-4 rounded h-10 w-full sm:w-auto" onclick="return confirm('Deseja realmente transferir este aluno?');">
                            Transferir Aluno
                        </button>
                    </div>
                </form>
            </div>
            @endif
            @endhasrole

            <!-- Lista de Alunos Inscritos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Alunos Inscritos ({{ $eletiva->alunosAtivos->count() }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white text-sm">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-left">Nome</th>
                                <th class="py-2 px-4 border-b text-left">RA</th>
                                <th class="py-2 px-4 border-b text-center">Data Inscrição</th>
                                <th class="py-2 px-4 border-b text-center">Status</th>
                                @hasrole('Gestor|Secretaria|Coordenador')
                                <th class="py-2 px-4 border-b text-center">Ações</th>
                                @endhasrole
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($eletiva->alunos as $aluno)
                                <tr class="{{ $aluno->pivot->status != 'Ativo' ? 'bg-gray-100 opacity-75' : '' }}">
                                    <td class="py-2 px-4 border-b">{{ $aluno->nome }}</td>
                                    <td class="py-2 px-4 border-b">{{ $aluno->ra }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ date('d/m/Y', strtotime($aluno->pivot->data_inscricao)) }}</td>
                                    <td class="py-2 px-4 border-b text-center">
                                        @if($aluno->pivot->status == 'Ativo')
                                            <span class="text-green-600 font-bold">Ativo</span>
                                        @elseif($aluno->pivot->status == 'Transferido')
                                            <span class="text-blue-600 font-bold" title="Transferido em {{ date('d/m/Y', strtotime($aluno->pivot->data_saida)) }}">Transferido</span>
                                        @else
                                            <span class="text-red-600 font-bold" title="Removido em {{ date('d/m/Y', strtotime($aluno->pivot->data_saida)) }}">Removido</span>
                                        @endif
                                    </td>
                                    
                                    @hasrole('Gestor|Secretaria|Coordenador')
                                    <td class="py-2 px-4 border-b text-center">
                                        @if($aluno->pivot->status == 'Ativo')
                                            <form action="{{ route('inscricao-eletiva.destroy', ['eletiva' => $eletiva->id, 'aluno' => $aluno->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este aluno desta eletiva?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">Remover</button>
                                            </form>
                                        @endif
                                    </td>
                                    @endhasrole
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">Nenhum aluno inscrito nesta eletiva/clube.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
