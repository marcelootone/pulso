<x-app-layout>
    <x-slot name="header">
        {{ __('Central de Alunos') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Acadêmico', 'url' => '#'],
            ['label' => 'Alunos']
        ]" />
    </x-slot>

    <x-slot name="actions">
        <x-button variant="secondary" onclick="window.location='{{ route('importar.index') }}'">
            <x-heroicon-o-arrow-up-tray class="w-4 h-4 mr-2" />
            Importar Estudantes
        </x-button>
    </x-slot>

    <x-card>
        <x-slot name="header">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <h3 class="text-lg font-bold text-gray-900">Lista de Alunos</h3>
                
                <form method="GET" action="{{ route('alunos.index') }}" class="w-full sm:w-1/3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                        </div>
                        <x-input type="text" name="search" value="{{ request('search') }}" class="pl-10 w-full" placeholder="Buscar por Nome ou RA..." />
                    </div>
                </form>
            </div>
        </x-slot>

        <div class="-mx-6 -my-6">
            <x-table>
                <x-slot name="head">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RA</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </x-slot>
                <x-slot name="body">
                    @forelse ($alunos as $aluno)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $aluno->ra }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $aluno->nome }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $aluno->status_matricula === 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $aluno->status_matricula }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('alunos.edit', $aluno->id) }}" class="text-primary-600 hover:text-primary-900 mr-3">Editar</a>
                                <form action="{{ route('alunos.destroy', $aluno->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir/desativar este aluno?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Nenhum aluno encontrado.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-table>
        </div>
        
        <x-slot name="footer">
            {{ $alunos->links() }}
        </x-slot>
    </x-card>
</x-app-layout>
