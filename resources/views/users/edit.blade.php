<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Funcionário:') }} {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-yellow-500">
                
                <div class="mb-6 flex justify-between items-center border-b pb-4">
                    <h3 class="text-xl font-black text-gray-800">Dados do Funcionário</h3>
                    <a href="{{ route('users.index') }}" class="text-sm text-yellow-700 hover:underline font-bold bg-yellow-50 px-3 py-2 rounded-md">⬅ Voltar para Funcionários</a>
                </div>

                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">Atenção!</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        {{-- Tipo de Usuário --}}
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Perfil de Acesso</label>
                            <select name="tipo_usuario" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 text-sm font-bold uppercase" required>
                                <option value="{{ \App\Models\User::TIPO_PROFESSOR }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_PROFESSOR ? 'selected' : '' }}>Professor(a)</option>
                                <option value="{{ \App\Models\User::TIPO_GESTOR }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_GESTOR ? 'selected' : '' }}>Gestor</option>
                                <option value="{{ \App\Models\User::TIPO_COORDENADOR }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_COORDENADOR ? 'selected' : '' }}>Coordenador</option>
                                <option value="{{ \App\Models\User::TIPO_SECRETARIA }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_SECRETARIA ? 'selected' : '' }}>Secretaria</option>
                                <option value="{{ \App\Models\User::TIPO_PROF_ESPECIAL }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_PROF_ESPECIAL ? 'selected' : '' }}>Prof. Educ. Especial</option>
                                <option value="{{ \App\Models\User::TIPO_PROF_ESTUDO_ORIENTADO }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_PROF_ESTUDO_ORIENTADO ? 'selected' : '' }}>Prof. Estudo Orientado</option>
                            </select>
                        </div>

                        {{-- CPF --}}
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">CPF</label>
                            <input type="text" name="cpf" value="{{ old('cpf', $user->cpf) }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 text-sm">
                        </div>
                    </div>

                    {{-- Dados Pessoais --}}
                    <h4 class="text-md font-bold text-gray-700 mb-3 border-b-2 border-gray-100 inline-block">1. Dados Pessoais</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Nome Completo *</label>
                            <input type="text" name="nome" value="{{ old('nome', $user->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Nascimento</label>
                                <input type="date" name="nascimento" value="{{ old('nascimento', $user->nascimento) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Sexo</label>
                                <select name="sexo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 text-sm">
                                    <option value="">...</option>
                                    <option value="M" {{ old('sexo', $user->sexo) == 'M' ? 'selected' : '' }}>M</option>
                                    <option value="F" {{ old('sexo', $user->sexo) == 'F' ? 'selected' : '' }}>F</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Telefone</label>
                            <input type="text" name="telefone" value="{{ old('telefone', $user->telefone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 text-sm">
                        </div>
                    </div>

                    {{-- Endereço --}}
                    <h4 class="text-md font-bold text-gray-700 mb-3 border-b-2 border-gray-100 inline-block">2. Endereço</h4>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Cidade</label>
                            <input type="text" name="cidade" value="{{ old('cidade', $user->cidade) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 text-sm">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Bairro</label>
                            <input type="text" name="bairro" value="{{ old('bairro', $user->bairro) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 text-sm">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Rua</label>
                            <input type="text" name="rua" value="{{ old('rua', $user->rua) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 text-sm">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Nº</label>
                            <input type="text" name="numero" value="{{ old('numero', $user->numero) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 text-sm">
                        </div>
                    </div>

                    {{-- Credenciais --}}
                    <h4 class="text-md font-bold text-gray-700 mb-3 border-b-2 border-gray-100 inline-block">3. Acesso ao Sistema</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded border border-gray-200 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">E-mail *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Nova Senha</label>
                            <input type="password" name="password" placeholder="Preencha apenas para alterar" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 text-sm">
                            <p class="text-[10px] text-gray-500 mt-1">Mínimo 6 caracteres. Deixe em branco para manter a senha atual.</p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t mt-6">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded shadow flex items-center transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            SALVAR ALTERAÇÕES
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
