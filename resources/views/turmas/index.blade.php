<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Turmas') }}
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

            {{-- Cabeçalho com botão de nova turma --}}
            <div class="mb-4 flex justify-between items-center">
                <div class="flex gap-3 items-center">
                    {{-- Filtro por status --}}
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'todas']) }}"
                       class="text-sm px-3 py-1 rounded-full border {{ !request('status') || request('status') === 'todas' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                        Todas
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'ativas']) }}"
                       class="text-sm px-3 py-1 rounded-full border {{ request('status') === 'ativas' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                        Ativas
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'inativas']) }}"
                       class="text-sm px-3 py-1 rounded-full border {{ request('status') === 'inativas' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                        Inativas
                    </a>
                </div>
                @hasrole('Gestor|Secretaria|Coordenador')
                    <a href="{{ route('turmas.create') }}" id="btn-nova-turma"
                       class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 font-bold transition">
                        + NOVA TURMA
                    </a>
                @endhasrole
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @forelse ($turmasPorModalidade as $modalidade => $turmas)
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2 uppercase tracking-wide">
                            {{ $modalidade }}
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse" id="tabela-turmas-{{ Str::slug($modalidade) }}">
                                <thead>
                                    <tr class="border-b-2 text-gray-600 text-sm uppercase bg-gray-50">
                                        <th class="py-3 px-2">Turno</th>
                                        <th class="py-3 px-2">Série / Compl.</th>
                                        <th class="py-3 px-2">Ano Letivo</th>
                                        <th class="py-3 px-2">Alunos</th>
                                        <th class="py-3 px-2">Status</th>
                                        <th class="py-3 px-2 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($turmas as $turma)
                                        <tr class="border-b hover:bg-gray-50 transition {{ $turma->ativa ? '' : 'opacity-60' }}">
                                            <td class="py-3 px-2 text-sm">{{ $turma->turno }}</td>
                                            <td class="py-3 px-2 font-bold">
                                                {{ $turma->serie }}º {{ $turma->complemento }}
                                            </td>
                                            <td class="py-3 px-2 text-sm text-gray-500">{{ $turma->ano_letivo }}</td>
                                            <td class="py-3 px-2 text-sm font-semibold text-indigo-600">
                                                {{ $turma->enturmacoes->count() }}
                                            </td>
                                            <td class="py-3 px-2">
                                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $turma->ativa ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $turma->ativa ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-2">
                                                <div class="flex justify-center items-center gap-2">
                                                    <a href="{{ route('turmas.show', $turma->id) }}"
                                                       class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md text-sm font-semibold transition">
                                                        Ver
                                                    </a>

                                                    @hasrole('Gestor|Secretaria|Coordenador')
                                                        <a href="{{ route('turmas.edit', $turma->id) }}"
                                                           id="btn-editar-turma-{{ $turma->id }}"
                                                           class="text-yellow-700 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 px-3 py-1 rounded-md text-sm font-semibold transition">
                                                            Editar
                                                        </a>

                                                        <form action="{{ route('turmas.destroy', $turma->id) }}" method="POST" class="inline"
                                                              onsubmit="return confirm('Deseja realmente alterar o status desta turma?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    id="btn-status-turma-{{ $turma->id }}"
                                                                    class="{{ $turma->ativa ? 'text-red-700 hover:text-red-900 bg-red-50 hover:bg-red-100' : 'text-green-700 hover:text-green-900 bg-green-50 hover:bg-green-100' }} px-3 py-1 rounded-md text-sm font-semibold transition">
                                                                {{ $turma->ativa ? '🚫 Desativar' : '✅ Reativar' }}
                                                            </button>
                                                        </form>
                                                    @endhasrole
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-400 italic">
                        Nenhuma turma encontrada.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>