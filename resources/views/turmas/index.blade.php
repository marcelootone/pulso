<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Turmas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4 flex justify-end">
                <a href="{{ route('turmas.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 font-bold">
                    + NOVA TURMA
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2">
                            <th class="py-2">Modalidade</th>
                            <th>Turno</th>
                            <th>Série/Complemento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($turmas as $turma)
                        <tr class="border-b">
                            <td class="py-2">{{ $turma->modalidade }}</td>
                            <td>{{ $turma->turno }}</td>
                            <td>{{ $turma->serie }}º {{ $turma->complemento }}</td>
                            <td>
                                <span class="px-2 py-1 rounded {{ $turma->ativa ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                    {{ $turma->ativa ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td>
                                @if(auth()->user()->tipo_usuario === 'Secretaria')
                                    <button class="text-red-600 hover:underline">Desativar</button>
                                @else
                                    <span class="text-gray-400 text-sm">Sem permissão</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>