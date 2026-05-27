<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Funcionários') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Feedback de sessão --}}
            @if(session('success'))
                <div id="alert-success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div id="alert-error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filtros e Botões --}}
            <div class="mb-4 flex justify-between items-center bg-white p-4 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('users.index') }}" class="flex gap-4 items-center">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Status</label>
                        <select name="status" class="text-sm rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                            <option value="todos" {{ request('status') == 'todos' ? 'selected' : '' }}>Todos</option>
                            <option value="ativos" {{ request('status') == 'ativos' ? 'selected' : '' }}>Ativos</option>
                            <option value="inativos" {{ request('status') == 'inativos' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Perfil</label>
                        <select name="tipo_usuario" class="text-sm rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                            <option value="">Todos os perfis</option>
                            <option value="{{ \App\Models\User::TIPO_PROFESSOR }}" {{ request('tipo_usuario') == \App\Models\User::TIPO_PROFESSOR ? 'selected' : '' }}>Professor</option>
                            <option value="{{ \App\Models\User::TIPO_GESTOR }}" {{ request('tipo_usuario') == \App\Models\User::TIPO_GESTOR ? 'selected' : '' }}>Gestor</option>
                            <option value="{{ \App\Models\User::TIPO_COORDENADOR }}" {{ request('tipo_usuario') == \App\Models\User::TIPO_COORDENADOR ? 'selected' : '' }}>Coordenador</option>
                            <option value="{{ \App\Models\User::TIPO_SECRETARIA }}" {{ request('tipo_usuario') == \App\Models\User::TIPO_SECRETARIA ? 'selected' : '' }}>Secretaria</option>
                        </select>
                    </div>
                    @if(request('status') || request('tipo_usuario'))
                        <div class="pt-5">
                            <a href="{{ route('users.index') }}" class="text-sm text-red-600 hover:underline font-semibold">Limpar Filtros</a>
                        </div>
                    @endif
                </form>

                <a href="{{ route('users.create') }}" id="btn-novo-funcionario" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 font-bold transition shadow-sm">
                    + NOVO FUNCIONÁRIO
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b-2 text-gray-600 text-sm uppercase">
                            <th class="p-4">Nome</th>
                            <th class="p-4">E-mail</th>
                            <th class="p-4">Perfil</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-b hover:bg-gray-50 transition {{ $user->ativo ? '' : 'opacity-60 bg-gray-100' }}">
                                <td class="p-4 font-semibold text-gray-800">{{ $user->name }}</td>
                                <td class="p-4 text-gray-600 text-sm">{{ $user->email }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-bold border border-indigo-100 uppercase">
                                        {{ $user->tipo_usuario }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    @if($user->ativo)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">🟢 Ativo</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">🔴 Inativo</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="{{ route('users.show', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md text-sm font-semibold transition">
                                            Ver
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-700 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 px-3 py-1 rounded-md text-sm font-semibold transition">
                                            ✏️ Editar
                                        </a>
                                        
                                        @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ $user->ativo ? 'Desativar este funcionário?' : 'Reativar este funcionário?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="{{ $user->ativo ? 'text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100' : 'text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100' }} px-3 py-1 rounded-md text-sm font-semibold transition">
                                                {{ $user->ativo ? '🚫 Desativar' : '✅ Reativar' }}
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 italic font-semibold">
                                    Nenhum funcionário encontrado com os filtros atuais.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
