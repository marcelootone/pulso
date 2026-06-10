<x-app-layout>
    <x-slot name="header">
        {{ __('Eletivas e Clubes') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Eletivas']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">
        
        @if (session('success'))
            <div class="mb-6">
                <x-alert type="success" message="{{ session('success') }}" />
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6">
                <x-alert type="error" message="{{ session('error') }}" />
            </div>
        @endif

        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <form action="{{ route('eletivas.index') }}" method="GET" class="w-full sm:w-auto">
                <x-select name="tipo" onchange="this.form.submit()" class="w-full sm:w-64">
                    <option value="">Todos os Tipos</option>
                    <option value="eletiva" {{ request('tipo') == 'eletiva' ? 'selected' : '' }}>Somente Eletivas</option>
                    <option value="clube" {{ request('tipo') == 'clube' ? 'selected' : '' }}>Somente Clubes</option>
                </x-select>
            </form>

            @can('gerenciar eletivas')
            <x-button variant="primary" onclick="window.location='{{ route('eletivas.create') }}'" class="w-full sm:w-auto justify-center">
                <x-heroicon-o-plus-circle class="w-5 h-5 mr-2" />
                Nova Eletiva/Clube
            </x-button>
            @endhasrole
        </div>

        <x-card>
            <div class="-mx-6 -my-6">
                <x-table>
                    <x-slot name="head">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professores</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Vagas Ocupadas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse ($eletivas as $eletiva)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $eletiva->nome }}</div>
                                    <div class="text-xs text-gray-500 mt-1 font-medium">Ano Letivo: {{ $eletiva->ano_letivo }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider {{ $eletiva->tipo == 'eletiva' ? 'bg-indigo-100 text-indigo-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ ucfirst($eletiva->tipo) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($eletiva->professores as $prof)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                <x-heroicon-o-user class="w-3 h-3 mr-1 text-gray-500" />
                                                {{ $prof->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $ocupadas = $eletiva->alunos_count;
                                        $total = $eletiva->vagas;
                                        $percent = $total > 0 ? ($ocupadas / $total) * 100 : 0;
                                        $colorClass = $percent >= 100 ? 'text-red-600' : ($percent >= 80 ? 'text-yellow-600' : 'text-green-600');
                                        $bgClass = $percent >= 100 ? 'bg-red-500' : ($percent >= 80 ? 'bg-yellow-500' : 'bg-green-500');
                                    @endphp
                                    <div class="text-sm font-black {{ $colorClass }}">
                                        {{ $ocupadas }} <span class="text-xs text-gray-400 font-medium">/ {{ $total }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 overflow-hidden">
                                        <div class="{{ $bgClass }} h-1.5 rounded-full transition-all duration-500" style="width: {{ min($percent, 100) }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($eletiva->ativa)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-green-100 text-green-800">
                                            Ativa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-red-100 text-red-800">
                                            Inativa
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center space-x-3">
                                        @if(auth()->user()->hasRole(['Gestor', 'Secretaria', 'Coordenador']) || $eletiva->professores->contains(auth()->id()))
                                        <a href="{{ route('eletivas.diario.show', $eletiva->id) }}" class="text-emerald-600 hover:text-emerald-900 transition-colors p-1" title="Diário de Frequência e Notas">
                                            <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                                        </a>
                                        @endif

                                        <a href="{{ route('eletivas.show', $eletiva->id) }}" class="text-primary-600 hover:text-primary-900 transition-colors p-1" title="Ver Detalhes">
                                            <x-heroicon-o-eye class="w-5 h-5" />
                                        </a>
                                        
                                        @can('gerenciar eletivas')
                                        <a href="{{ route('eletivas.edit', $eletiva->id) }}" class="text-amber-500 hover:text-amber-700 transition-colors p-1" title="Editar">
                                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                                        </a>
                                        @endcan

                                        @can('gerenciar eletivas')
                                        <form action="{{ route('eletivas.destroy', $eletiva->id) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Tem certeza que deseja {{ $eletiva->ativa ? 'desativar' : 'ativar' }} esta disciplina?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="{{ $eletiva->ativa ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700' }} transition-colors p-1" title="{{ $eletiva->ativa ? 'Desativar' : 'Ativar' }}">
                                                @if($eletiva->ativa)
                                                    <x-heroicon-o-no-symbol class="w-5 h-5" />
                                                @else
                                                    <x-heroicon-o-check-circle class="w-5 h-5" />
                                                @endif
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center border-dashed border-2 border-gray-200 rounded-lg">
                                    <x-heroicon-o-book-open class="mx-auto h-12 w-12 text-gray-300 mb-3" />
                                    <h3 class="text-sm font-medium text-gray-900">Nenhum registro encontrado</h3>
                                    <p class="mt-1 text-sm text-gray-500">Tente ajustar os filtros ou crie uma nova eletiva/clube.</p>
                                </td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-table>
            </div>
            
            @if($eletivas->hasPages())
                <x-slot name="footer">
                    <div class="-mx-6 -my-4 bg-gray-50 px-6 py-4 border-t">
                        {{ $eletivas->links() }}
                    </div>
                </x-slot>
            @endif
        </x-card>
    </div>
</x-app-layout>
