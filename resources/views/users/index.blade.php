<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestão de Funcionários') }}
            </h2>
            <x-button variant="primary" onclick="window.location='{{ route('users.create') }}'" class="w-full sm:w-auto justify-center">
                <x-heroicon-o-user-plus class="w-5 h-5 mr-2" /> Novo Usuário
            </x-button>
        </div>
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Central de Cadastros', 'url' => '#'],
            ['label' => 'Funcionários']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">

        {{-- Feedback de sessão --}}
        @if(session('success'))
            <div class="mb-6">
                <x-alert type="success" message="{{ session('success') }}" />
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6">
                <x-alert type="error" message="{{ session('error') }}" />
            </div>
        @endif

        {{-- Filtros --}}
        <x-card class="mb-6 !p-4">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="w-full sm:w-1/4">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status</label>
                    <x-select name="status" class="w-full" onchange="this.form.submit()">
                        <option value="todos" {{ request('status') == 'todos' ? 'selected' : '' }}>Todos os Status</option>
                        <option value="ativos" {{ request('status') == 'ativos' ? 'selected' : '' }}>Somente Ativos</option>
                        <option value="inativos" {{ request('status') == 'inativos' ? 'selected' : '' }}>Somente Inativos</option>
                    </x-select>
                </div>
                <div class="w-full sm:w-1/3">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Perfil de Acesso</label>
                    <x-select name="tipo_usuario" class="w-full" onchange="this.form.submit()">
                        <option value="">Todos os perfis</option>
                        <option value="{{ \App\Models\User::TIPO_PROFESSOR }}" {{ request('tipo_usuario') == \App\Models\User::TIPO_PROFESSOR ? 'selected' : '' }}>Professor(a)</option>
                        <option value="{{ \App\Models\User::TIPO_GESTOR }}" {{ request('tipo_usuario') == \App\Models\User::TIPO_GESTOR ? 'selected' : '' }}>Gestor(a)</option>
                        <option value="{{ \App\Models\User::TIPO_COORDENADOR }}" {{ request('tipo_usuario') == \App\Models\User::TIPO_COORDENADOR ? 'selected' : '' }}>Coordenador(a)</option>
                        <option value="{{ \App\Models\User::TIPO_SECRETARIA }}" {{ request('tipo_usuario') == \App\Models\User::TIPO_SECRETARIA ? 'selected' : '' }}>Secretaria</option>
                    </x-select>
                </div>
                @if(request('status') || request('tipo_usuario'))
                    <div class="w-full sm:w-auto">
                        <x-button variant="secondary" onclick="window.location='{{ route('users.index') }}'" type="button" class="w-full justify-center">
                            Limpar Filtros
                        </x-button>
                    </div>
                @endif
            </form>
        </x-card>

        <x-card class="!p-0 border-t-4 border-t-primary-500 overflow-hidden">
            <x-table>
                <x-slot name="head">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">E-mail</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </x-slot>
                <x-slot name="body">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors {{ !$user->ativo ? 'bg-gray-50/50 opacity-75' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 flex items-center">
                                    <div class="w-8 h-8 rounded-full {{ $user->ativo ? 'bg-primary-100 text-primary-600' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center mr-3 font-black text-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        {{ $user->name }}
                                        <div class="text-xs text-gray-500 font-normal md:hidden mt-0.5">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell text-sm text-gray-600 font-medium">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-md text-xs font-bold border border-indigo-100 uppercase tracking-wider">
                                    {{ $user->tipo_usuario }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($user->ativo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-green-100 text-green-800 border border-green-200">
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('users.show', $user->id) }}" class="text-primary-600 hover:text-primary-900 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-md transition-colors" title="Ver Detalhes">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-amber-600 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-md transition-colors" title="Editar">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                    
                                    @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ $user->ativo ? 'Desativar este funcionário?' : 'Reativar este funcionário?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="{{ $user->ativo ? 'text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100' : 'text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100' }} px-3 py-1.5 rounded-md transition-colors" title="{{ $user->ativo ? 'Desativar' : 'Reativar' }}">
                                            @if($user->ativo)
                                                <x-heroicon-o-no-symbol class="w-4 h-4" />
                                            @else
                                                <x-heroicon-o-check-circle class="w-4 h-4" />
                                            @endif
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 border-dashed border-2 border-gray-200 rounded-lg bg-gray-50">
                                <x-heroicon-o-users class="mx-auto h-12 w-12 text-gray-300 mb-3" />
                                <p class="text-base font-bold text-gray-500 mb-1">Nenhum funcionário encontrado.</p>
                                <p class="text-sm font-medium">Tente alterar os filtros ou cadastrar um novo funcionário.</p>
                            </td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-table>
        </x-card>
    </div>
</x-app-layout>
