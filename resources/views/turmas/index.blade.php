<x-app-layout>
    <x-slot name="header">
        {{ __('Gestão de Turmas') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Acadêmico', 'url' => '#'],
            ['label' => 'Turmas']
        ]" />
    </x-slot>

    <x-slot name="actions">
        @can('gerenciar turmas')
            <x-button variant="primary" onclick="window.location='{{ route('turmas.create') }}'">
                <x-icon name="heroicon-o-plus" class="w-4 h-4 mr-2" />
                Nova Turma
            </x-button>
        @endhasrole
    </x-slot>

    <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <!-- Filtros de Status -->
        <div class="flex gap-3 items-center">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'todas']) }}"
                class="text-sm px-4 py-1.5 rounded-full font-medium transition-colors border {{ !request('status') || request('status') === 'todas' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                Todas
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'ativas']) }}"
                class="text-sm px-4 py-1.5 rounded-full font-medium transition-colors border {{ request('status') === 'ativas' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                Ativas
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'inativas']) }}"
                class="text-sm px-4 py-1.5 rounded-full font-medium transition-colors border {{ request('status') === 'inativas' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                Inativas
            </a>
        </div>

        <!-- Barra de Pesquisa -->
        <form method="GET" action="{{ route('turmas.index') }}" class="w-full sm:w-auto relative">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-s-magnifying-glass class="h-5 w-5 text-gray-400" />
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar turmas, alunos, docentes..."
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition duration-150 ease-in-out">
                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <x-heroicon-s-x-mark class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    @forelse ($turmasPorModalidade as $modalidade => $turmas)
        <x-card class="mb-8">
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide">
                    {{ $modalidade }}
                </h3>
            </x-slot>

            <div class="-mx-6 -my-6">
                <x-table>
                    <x-slot name="head">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turno</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Série / Compl.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docentes</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ano Letivo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Alunos</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </x-slot>
                    <x-slot name="body">
                        @foreach ($turmas as $turma)
                            @php
                                $professoresRegulares = $turma->professores->where('tipo_usuario', \App\Models\User::TIPO_PROFESSOR)->pluck('name')->join(', ');
                                $professoresEO = $turma->professores->where('tipo_usuario', \App\Models\User::TIPO_PROF_ESTUDO_ORIENTADO)->pluck('name')->join(', ');
                            @endphp
                            <tr class="hover:bg-gray-50 transition {{ $turma->ativa ? '' : 'opacity-60' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $turma->turno }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    {{ $turma->serie }}º {{ $turma->complemento }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($professoresRegulares)
                                        <div class="mb-1">
                                            <span class="font-semibold text-gray-500 text-xs uppercase tracking-wider">Prof:</span>
                                            {{ $professoresRegulares }}
                                        </div>
                                    @endif
                                    @if($professoresEO)
                                        <div>
                                            <span class="font-semibold text-gray-500 text-xs uppercase tracking-wider">Orientador:</span>
                                            {{ $professoresEO }}
                                        </div>
                                    @endif
                                    @if(!$professoresRegulares && !$professoresEO)
                                        <span class="text-gray-400 italic">Nenhum</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">{{ $turma->ano_letivo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-primary-600">
                                    {{ $turma->enturmacoes_count ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $turma->ativa ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $turma->ativa ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center gap-3">
                                        <a href="{{ route('turmas.show', $turma->id) }}" class="text-primary-600 hover:text-primary-900 transition-colors">
                                            Ver
                                        </a>
                                        @can('gerenciar turmas')
                                            <a href="{{ route('turmas.edit', $turma->id) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                                Editar
                                            </a>
                                            <form action="{{ route('turmas.destroy', $turma->id) }}" method="POST" class="inline" onsubmit="return confirm('Deseja realmente alterar o status desta turma?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="{{ $turma->ativa ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }} transition-colors">
                                                    {{ $turma->ativa ? 'Desativar' : 'Reativar' }}
                                                </button>
                                            </form>
                                        @endhasrole
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-table>
            </div>
        </x-card>
    @empty
        <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
            <x-icon name="heroicon-o-academic-cap" class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma turma encontrada</h3>
            <p class="mt-1 text-sm text-gray-500">Comece criando uma nova turma para iniciar o ano letivo.</p>
            @can('gerenciar turmas')
                <div class="mt-6">
                    <x-button variant="primary" onclick="window.location='{{ route('turmas.create') }}'">
                        <x-icon name="heroicon-o-plus" class="w-4 h-4 mr-2" /> Nova Turma
                    </x-button>
                </div>
            @endhasrole
        </div>
    @endforelse
</x-app-layout>
