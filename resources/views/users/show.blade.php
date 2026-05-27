<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes do Funcionário:') }} {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 {{ $user->ativo ? 'border-green-500' : 'border-red-500' }}">
                
                <div class="mb-6 flex justify-between items-center border-b pb-4">
                    <div class="flex items-center gap-4">
                        <h3 class="text-2xl font-black text-gray-800">{{ $user->name }}</h3>
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold uppercase border border-indigo-100">
                            {{ $user->tipo_usuario }}
                        </span>
                        @if($user->ativo)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">🟢 Ativo</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">🔴 Inativo</span>
                        @endif
                    </div>
                    <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:underline font-bold bg-gray-50 px-3 py-2 rounded-md">⬅ Voltar</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Coluna 1: Dados de Acesso e Pessoais --}}
                    <div>
                        <h4 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Credenciais</h4>
                        <ul class="text-sm space-y-3 mb-8">
                            <li><strong class="text-gray-500 block uppercase text-xs">E-mail de Acesso:</strong> <span class="font-medium text-gray-800">{{ $user->email }}</span></li>
                            <li><strong class="text-gray-500 block uppercase text-xs">CPF:</strong> <span class="font-medium text-gray-800">{{ $user->cpf ?: 'Não informado' }}</span></li>
                        </ul>

                        <h4 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Dados Pessoais</h4>
                        <ul class="text-sm space-y-3">
                            <li><strong class="text-gray-500 block uppercase text-xs">Nascimento:</strong> <span class="font-medium text-gray-800">{{ $user->nascimento ? \Carbon\Carbon::parse($user->nascimento)->format('d/m/Y') : 'Não informado' }}</span></li>
                            <li><strong class="text-gray-500 block uppercase text-xs">Sexo:</strong> <span class="font-medium text-gray-800">{{ $user->sexo ?: 'Não informado' }}</span></li>
                            <li><strong class="text-gray-500 block uppercase text-xs">Telefone:</strong> <span class="font-medium text-gray-800">{{ $user->telefone ?: 'Não informado' }}</span></li>
                        </ul>
                    </div>

                    {{-- Coluna 2: Endereço e Ações --}}
                    <div>
                        <h4 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Endereço</h4>
                        <ul class="text-sm space-y-3 mb-8 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <li><strong class="text-gray-500 block uppercase text-xs">Cidade:</strong> <span class="font-medium text-gray-800">{{ $user->cidade ?: '-' }}</span></li>
                            <li><strong class="text-gray-500 block uppercase text-xs">Bairro:</strong> <span class="font-medium text-gray-800">{{ $user->bairro ?: '-' }}</span></li>
                            <li><strong class="text-gray-500 block uppercase text-xs">Logradouro:</strong> <span class="font-medium text-gray-800">{{ $user->rua ?: '-' }}, Nº {{ $user->numero ?: 'S/N' }}</span></li>
                        </ul>

                        <div class="mt-8 flex flex-col gap-3">
                            <a href="{{ route('users.edit', $user->id) }}" class="text-center bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-bold py-2 px-4 rounded border border-yellow-200 transition">
                                ✏️ Editar Dados do Funcionário
                            </a>
                            
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ $user->ativo ? 'Deseja realmente DESATIVAR este funcionário?' : 'Deseja REATIVAR este funcionário?' }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-center {{ $user->ativo ? 'bg-red-50 hover:bg-red-100 text-red-700 border-red-200' : 'bg-green-50 hover:bg-green-100 text-green-700 border-green-200' }} font-bold py-2 px-4 rounded border transition">
                                    {{ $user->ativo ? '🚫 Desativar Acesso do Funcionário' : '✅ Reativar Acesso do Funcionário' }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
