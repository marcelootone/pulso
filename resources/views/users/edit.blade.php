<x-app-layout>
    <x-slot name="header">
        {{ __('Editar Funcionário') }}: {{ $user->name }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Central de Cadastros', 'url' => '#'],
            ['label' => 'Funcionários', 'url' => route('users.index')],
            ['label' => 'Editar']
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <x-card class="border-t-4 border-t-amber-500">
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <x-heroicon-o-pencil-square class="w-6 h-6 text-amber-500 mr-2" />
                        Atualizar Dados do Funcionário
                    </h3>
                    <x-button variant="secondary" onclick="window.location='{{ route('users.index') }}'" class="text-sm">
                        <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" /> Voltar
                    </x-button>
                </div>
            </x-slot>

            @if ($errors->any())
                <div class="mb-6">
                    <x-alert type="error">
                        <strong class="font-bold">Atenção!</strong>
                        <ul class="mt-2 list-disc list-inside text-sm font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                </div>
            @endif

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    {{-- Tipo de Usuário --}}
                    <div class="bg-amber-50/50 p-5 rounded-xl border border-amber-100">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Perfil de Acesso <span class="text-red-500">*</span></label>
                        <x-select name="tipo_usuario" required class="w-full bg-white font-bold text-primary-700">
                            <option value="{{ \App\Models\User::TIPO_PROFESSOR }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_PROFESSOR ? 'selected' : '' }}>Professor(a)</option>
                            <option value="{{ \App\Models\User::TIPO_GESTOR }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_GESTOR ? 'selected' : '' }}>Gestor</option>
                            <option value="{{ \App\Models\User::TIPO_COORDENADOR }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_COORDENADOR ? 'selected' : '' }}>Coordenador</option>
                            <option value="{{ \App\Models\User::TIPO_SECRETARIA }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_SECRETARIA ? 'selected' : '' }}>Secretaria</option>
                            <option value="{{ \App\Models\User::TIPO_PROF_ESPECIAL }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_PROF_ESPECIAL ? 'selected' : '' }}>Prof. Educ. Especial</option>
                            <option value="{{ \App\Models\User::TIPO_PROF_ESTUDO_ORIENTADO }}" {{ old('tipo_usuario', $user->tipo_usuario) == \App\Models\User::TIPO_PROF_ESTUDO_ORIENTADO ? 'selected' : '' }}>Prof. Estudo Orientado</option>
                        </x-select>
                    </div>

                    {{-- CPF --}}
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">CPF</label>
                        <x-input type="text" name="cpf" value="{{ old('cpf', $user->cpf) }}" class="w-full bg-white" placeholder="000.000.000-00" />
                    </div>
                </div>

                {{-- Dados Pessoais --}}
                <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b-2 border-gray-100 flex items-center">
                    <span class="bg-gray-100 text-gray-600 rounded-full w-6 h-6 inline-flex items-center justify-center mr-2 text-xs">1</span>
                    Dados Pessoais
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nome Completo <span class="text-red-500">*</span></label>
                        <x-input type="text" name="nome" value="{{ old('nome', $user->name) }}" required class="w-full" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nascimento</label>
                            <x-input type="date" name="nascimento" value="{{ old('nascimento', $user->nascimento) }}" max="{{ date('Y-m-d') }}" class="w-full" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Sexo</label>
                            <x-select name="sexo" class="w-full">
                                <option value="">...</option>
                                <option value="M" {{ old('sexo', $user->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('sexo', $user->sexo) == 'F' ? 'selected' : '' }}>Feminino</option>
                            </x-select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Telefone</label>
                        <x-input type="text" name="telefone" value="{{ old('telefone', $user->telefone) }}" class="w-full" oninput="this.value = this.value.replace(/[^0-9\(\)\-\+\s]/g, '')" />
                    </div>
                </div>

                {{-- Endereço --}}
                <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b-2 border-gray-100 flex items-center">
                    <span class="bg-gray-100 text-gray-600 rounded-full w-6 h-6 inline-flex items-center justify-center mr-2 text-xs">2</span>
                    Endereço
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-8">
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Cidade</label>
                        <x-input type="text" name="cidade" value="{{ old('cidade', $user->cidade) }}" class="w-full" />
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Bairro</label>
                        <x-input type="text" name="bairro" value="{{ old('bairro', $user->bairro) }}" class="w-full" />
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Logradouro</label>
                        <x-input type="text" name="rua" value="{{ old('rua', $user->rua) }}" class="w-full" />
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nº</label>
                        <x-input type="text" name="numero" value="{{ old('numero', $user->numero) }}" class="w-full" />
                    </div>
                </div>

                {{-- Credenciais --}}
                <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b-2 border-gray-100 flex items-center">
                    <span class="bg-gray-100 text-gray-600 rounded-full w-6 h-6 inline-flex items-center justify-center mr-2 text-xs">3</span>
                    Acesso ao Sistema
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-red-50/50 p-6 rounded-xl border border-red-100 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">E-mail de Acesso <span class="text-red-500">*</span></label>
                        <x-input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-white focus:ring-red-500 focus:border-red-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nova Senha</label>
                        <x-input type="password" name="password" placeholder="Preencha apenas para alterar" class="w-full bg-white focus:ring-yellow-500 focus:border-yellow-500" />
                        <p class="text-[11px] font-medium text-gray-500 mt-2 flex items-center">
                            <x-heroicon-o-information-circle class="w-4 h-4 mr-1 text-gray-400" />
                            Mínimo 6 caracteres. Deixe em branco para manter a senha atual.
                        </p>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <x-button variant="secondary" type="button" onclick="window.location='{{ route('users.index') }}'">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit" class="!bg-amber-500 hover:!bg-amber-600 border-none">
                            <x-heroicon-o-check class="w-5 h-5 mr-2" /> Salvar Alterações
                        </x-button>
                    </div>
                </x-slot>
            </form>
        </x-card>
    </div>
</x-app-layout>
