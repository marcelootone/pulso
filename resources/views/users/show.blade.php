<x-app-layout>
    <x-slot name="header">
        {{ __('Detalhes do Funcionário') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Central de Cadastros', 'url' => '#'],
            ['label' => 'Funcionários', 'url' => route('users.index')],
            ['label' => 'Detalhes']
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <x-card class="border-t-4 {{ $user->ativo ? 'border-t-emerald-500' : 'border-t-red-500' }}">
            <x-slot name="header">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full {{ $user->ativo ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center font-black text-xl shadow-sm border {{ $user->ativo ? 'border-emerald-200' : 'border-red-200' }}">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 leading-tight">{{ $user->name }}</h3>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="px-2.5 py-0.5 bg-primary-50 text-primary-700 rounded-md text-[10px] font-bold uppercase border border-primary-100 tracking-wider">
                                    {{ $user->tipo_usuario }}
                                </span>
                                @if($user->ativo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-800 border border-green-200">
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <x-button variant="secondary" onclick="window.location='{{ route('users.index') }}'" class="shrink-0 text-sm">
                        <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" /> Voltar
                    </x-button>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Coluna 1: Dados de Acesso e Pessoais --}}
                <div class="space-y-8">
                    <div>
                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b-2 border-gray-100 flex items-center">
                            <x-heroicon-o-key class="w-5 h-5 text-gray-400 mr-2" /> Credenciais
                        </h4>
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 space-y-4">
                            <div>
                                <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">E-mail de Acesso</span>
                                <span class="text-sm font-bold text-gray-900">{{ $user->email }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">CPF</span>
                                <span class="text-sm font-bold text-gray-900">{{ $user->cpf ?: 'Não informado' }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b-2 border-gray-100 flex items-center">
                            <x-heroicon-o-user class="w-5 h-5 text-gray-400 mr-2" /> Dados Pessoais
                        </h4>
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nascimento</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $user->nascimento ? \Carbon\Carbon::parse($user->nascimento)->format('d/m/Y') : 'Não informado' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Sexo</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $user->sexo === 'M' ? 'Masculino' : ($user->sexo === 'F' ? 'Feminino' : 'Não informado') }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Telefone</span>
                                <span class="text-sm font-bold text-gray-900">{{ $user->telefone ?: 'Não informado' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Coluna 2: Endereço e Ações --}}
                <div class="space-y-8 flex flex-col h-full">
                    <div>
                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b-2 border-gray-100 flex items-center">
                            <x-heroicon-o-map-pin class="w-5 h-5 text-gray-400 mr-2" /> Endereço
                        </h4>
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cidade</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $user->cidade ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Bairro</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $user->bairro ?: '-' }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Logradouro</span>
                                <span class="text-sm font-bold text-gray-900">{{ $user->rua ?: '-' }}{{ $user->numero ? ', Nº ' . $user->numero : '' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto pt-6">
                        <div class="flex flex-col gap-3">
                            <x-button variant="secondary" onclick="window.location='{{ route('users.edit', $user->id) }}'" class="w-full justify-center !text-amber-700 !bg-amber-50 hover:!bg-amber-100 border border-amber-200">
                                <x-heroicon-o-pencil-square class="w-5 h-5 mr-2" /> Editar Dados do Funcionário
                            </x-button>
                            
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ $user->ativo ? 'Deseja realmente DESATIVAR este funcionário?' : 'Deseja REATIVAR este funcionário?' }}')">
                                @csrf
                                @method('DELETE')
                                <x-button variant="secondary" type="submit" class="w-full justify-center {{ $user->ativo ? '!text-red-700 !bg-red-50 hover:!bg-red-100 border-red-200' : '!text-emerald-700 !bg-emerald-50 hover:!bg-emerald-100 border-emerald-200' }}">
                                    @if($user->ativo)
                                        <x-heroicon-o-no-symbol class="w-5 h-5 mr-2" /> Desativar Acesso do Funcionário
                                    @else
                                        <x-heroicon-o-check-circle class="w-5 h-5 mr-2" /> Reativar Acesso do Funcionário
                                    @endif
                                </x-button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</x-app-layout>
