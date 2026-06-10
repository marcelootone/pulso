<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            {{ __('Detalhes') }} - {{ $eletiva->nome }} 
            <span class="ml-3 px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-md {{ $eletiva->tipo == 'eletiva' ? 'bg-indigo-100 text-indigo-800' : 'bg-purple-100 text-purple-800' }}">
                {{ ucfirst($eletiva->tipo) }}
            </span>
        </div>
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Eletivas', 'url' => route('eletivas.index')],
            ['label' => 'Detalhes']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        @if (session('success'))
            <x-alert type="success" message="{{ session('success') }}" />
        @endif
        @if (session('error'))
            <x-alert type="error" message="{{ session('error') }}" />
        @endif

        <!-- Resumo e Professores -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informações -->
            <x-card class="border-t-4 border-t-primary-500 h-full">
                <x-slot name="header">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <x-heroicon-o-information-circle class="w-5 h-5 mr-2 text-primary-500" />
                        Informações
                    </h3>
                </x-slot>
                <div class="space-y-4 text-sm">
                    <div>
                        <strong class="block text-gray-500 uppercase tracking-wider text-xs mb-1">Descrição:</strong> 
                        <p class="text-gray-900">{{ $eletiva->descricao ?? 'Nenhuma descrição informada.' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <strong class="block text-gray-500 uppercase tracking-wider text-xs mb-1">Vagas:</strong>
                            <span class="text-lg font-black text-gray-900">{{ $eletiva->alunosAtivos->count() }} <span class="text-sm font-medium text-gray-500">/ {{ $eletiva->vagas }}</span></span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <strong class="block text-gray-500 uppercase tracking-wider text-xs mb-1">Status:</strong>
                            @if($eletiva->ativa)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800 mt-1">Ativa</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 mt-1">Inativa</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <strong class="block text-gray-500 uppercase tracking-wider text-xs mb-1">Sistema de Notas:</strong> 
                        <span class="font-semibold text-gray-900">{{ $eletiva->usa_nota ? 'Habilitado (Professor poderá lançar notas)' : 'Desabilitado' }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Professores -->
            <x-card class="border-t-4 border-t-indigo-500 h-full">
                <x-slot name="header">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <x-heroicon-o-users class="w-5 h-5 mr-2 text-indigo-500" />
                        Professores Responsáveis
                    </h3>
                </x-slot>
                <div class="space-y-3">
                    @forelse($eletiva->professores as $prof)
                        <div class="flex items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold mr-3">
                                {{ substr($prof->name, 0, 1) }}
                            </div>
                            <span class="font-semibold text-gray-900">{{ $prof->name }}</span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-500 italic">
                            Nenhum professor vinculado.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Inscrição de Alunos (Apenas Gestor, Secretaria, Coordenador) -->
        @can('gerenciar eletivas')
        <div class="grid grid-cols-1 {{ $eletiva->tipo === 'clube' ? 'lg:grid-cols-2' : '' }} gap-6">
            <x-card class="border-t-4 border-t-emerald-500 h-full">
                <x-slot name="header">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <x-heroicon-o-user-plus class="w-5 h-5 mr-2 text-emerald-500" />
                        Vincular Alunos
                    </h3>
                </x-slot>
                @if($eletiva->alunosAtivos->count() >= $eletiva->vagas)
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200 flex items-center">
                        <x-heroicon-o-x-circle class="w-6 h-6 text-red-500 mr-2" />
                        <span class="text-red-700 font-bold">Limite de vagas atingido. Não é possível inscrever mais alunos.</span>
                    </div>
                @else
                    <form action="{{ route('inscricao-eletiva.store', $eletiva->id) }}" method="POST">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4 items-end">
                            <div class="flex-grow w-full">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Selecione os Alunos</label>
                                <select name="aluno_ids[]" multiple required class="w-full border-gray-300 rounded-md shadow-sm h-40 focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach($alunosParaInscricao as $aluno)
                                        <option value="{{ $aluno->id }}" class="py-1 px-2 border-b">{{ $aluno->nome }} (RA: {{ $aluno->ra }})</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-2 font-medium">Pressione CTRL (ou CMD) para selecionar múltiplos alunos.</p>
                            </div>
                            <x-button variant="primary" class="!bg-emerald-600 hover:!bg-emerald-700 w-full sm:w-auto h-11 justify-center border-none">
                                <x-heroicon-o-plus class="w-5 h-5 mr-1" /> Inscrever
                            </x-button>
                        </div>
                    </form>
                @endif
            </x-card>
            
            @if($eletiva->tipo === 'clube')
            <x-card class="border-t-4 border-t-purple-500 h-full">
                <x-slot name="header">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <x-heroicon-o-arrows-right-left class="w-5 h-5 mr-2 text-purple-500" />
                        Troca de Clube (Transferência)
                    </h3>
                </x-slot>
                <form action="{{ route('inscricao-eletiva.trocar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="clube_origem_id" value="{{ $eletiva->id }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Aluno a Transferir</label>
                            <x-select name="aluno_id" required class="w-full">
                                <option value="">Selecione...</option>
                                @foreach($eletiva->alunosAtivos as $aluno)
                                    <option value="{{ $aluno->id }}">{{ $aluno->nome }}</option>
                                @endforeach
                            </x-select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Clube de Destino</label>
                            <x-select name="clube_destino_id" required class="w-full">
                                <option value="">Selecione...</option>
                                @foreach(\App\Models\Eletiva::where('tipo', 'clube')->where('ativa', true)->where('id', '!=', $eletiva->id)->get() as $outroClube)
                                    <option value="{{ $outroClube->id }}">{{ $outroClube->nome }} ({{ $outroClube->alunosAtivos->count() }}/{{ $outroClube->vagas }})</option>
                                @endforeach
                            </x-select>
                        </div>
                        <div class="pt-2 text-right">
                            <x-button variant="primary" class="!bg-purple-600 hover:!bg-purple-700 w-full sm:w-auto justify-center border-none" onclick="return confirm('Deseja realmente transferir este aluno?');">
                                Transferir Aluno
                            </x-button>
                        </div>
                    </div>
                </form>
            </x-card>
            @endif
        </div>
        @endhasrole

        <!-- Lista de Alunos Inscritos -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <x-heroicon-o-user-group class="w-5 h-5 mr-2 text-gray-600" />
                    Alunos Inscritos <span class="ml-2 bg-gray-200 text-gray-800 py-0.5 px-2.5 rounded-full text-xs">{{ $eletiva->alunosAtivos->count() }}</span>
                </h3>
            </x-slot>
            
            <div class="-mx-6 -my-6">
                <x-table>
                    <x-slot name="head">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RA</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Data Inscrição</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        @can('vincular estudantes eletivas')
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        @endcan
                    </x-slot>
                    <x-slot name="body">
                        @forelse ($eletiva->alunos as $aluno)
                            <tr class="hover:bg-gray-50 transition-colors {{ $aluno->pivot->status != 'Ativo' ? 'bg-gray-50 opacity-75' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 text-sm">{{ $aluno->nome }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-500 text-sm">{{ $aluno->ra }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">{{ date('d/m/Y', strtotime($aluno->pivot->data_inscricao)) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    @if($aluno->pivot->status == 'Ativo')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-green-100 text-green-800">Ativo</span>
                                    @elseif($aluno->pivot->status == 'Transferido')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-blue-100 text-blue-800" title="Transferido em {{ date('d/m/Y', strtotime($aluno->pivot->data_saida)) }}">Transferido</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-red-100 text-red-800" title="Removido em {{ date('d/m/Y', strtotime($aluno->pivot->data_saida)) }}">Removido</span>
                                    @endif
                                </td>
                                
                                @can('vincular estudantes eletivas')
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    @if($aluno->pivot->status == 'Ativo')
                                        <form action="{{ route('inscricao-eletiva.destroy', ['eletiva' => $eletiva->id, 'aluno' => $aluno->id]) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover este aluno desta eletiva?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors" title="Remover Aluno">
                                                <x-heroicon-o-trash class="w-4 h-4 inline" /> Remover
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center border-dashed border-2 border-gray-200 rounded-lg">
                                    <x-heroicon-o-users class="mx-auto h-12 w-12 text-gray-300 mb-3" />
                                    <p class="text-sm font-medium text-gray-500">Nenhum aluno inscrito nesta eletiva/clube.</p>
                                </td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-table>
            </div>
        </x-card>
    </div>
</x-app-layout>
