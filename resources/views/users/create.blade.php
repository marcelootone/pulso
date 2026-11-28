<x-app-layout>
    <x-slot name="header">
        {{ __('Cadastro Manual de Usuário') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Central de Cadastros', 'url' => '#'],
            ['label' => 'Funcionários', 'url' => route('users.index')],
            ['label' => 'Novo Usuário']
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto">

        <!-- ALERTA DE DUPLICATA -->
        <div class="mb-6">
            <x-alert type="info">
                <div class="flex items-center">
                    <strong>ATENÇÃO:</strong>&nbsp; Sempre verifique se o usuário já existe para evitar duplicatas. Para verificar, faça uma busca pelo nome na listagem antes de criar um novo.
                </div>
            </x-alert>
        </div>

        <!-- TABS -->
        <div class="flex mb-6 border-b border-gray-200">
            <a href="{{ route('users.create') }}" class="text-primary-600 border-b-2 border-primary-600 px-6 py-3 font-bold text-sm flex items-center transition-colors">
                <x-icon name="heroicon-o-user"-plus class="w-5 h-5 mr-2" />
                Criar Manualmente
            </a>
            <a href="{{ route('importar.index') }}" class="text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent px-6 py-3 font-bold text-sm flex items-center transition-colors">
                <x-icon name="heroicon-o-arrow-up-tray" class="w-5 h-5 mr-2" />
                Importar Planilha (Estudantes)
            </a>
        </div>

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

        @if ($errors->any())
            <div class="mb-6">
                <x-alert type="error">
                    <strong class="font-bold">Opa! Algum erro ocorreu:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- BLOCO 1: TIPO DE USUARIO E DOCUMENTOS -->
                <x-card class="border-t-4 border-t-emerald-500">
                    <x-slot name="header">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <x-icon name="heroicon-o-identification" class="w-5 h-5 mr-2 text-emerald-500" />
                            Tipo de Perfil e Documentos
                        </h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tipo de Usuário <span class="text-red-500">*</span></label>
                            <x-select name="tipo_usuario" class="w-full bg-emerald-50 border-emerald-200 text-emerald-800 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                                <option value="{{ \App\Models\User::TIPO_PROFESSOR }}">PROFESSOR(A)</option>
                                <option value="{{ \App\Models\User::TIPO_GESTOR }}">GESTOR</option>
                                <option value="{{ \App\Models\User::TIPO_COORDENADOR }}">COORDENADOR</option>
                                <option value="{{ \App\Models\User::TIPO_SECRETARIA }}">SECRETARIA</option>
                                <option value="{{ \App\Models\User::TIPO_PEDAGOGO }}">PEDAGOGO</option>
                                <option value="{{ \App\Models\User::TIPO_PROF_ESPECIAL }}">PROFESSOR EDUCAÇÃO ESPECIAL</option>
                                <option value="{{ \App\Models\User::TIPO_PROF_ESTUDO_ORIENTADO }}">PROFESSOR DE ESTUDO ORIENTADO</option>
                            </x-select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Documento (CPF)</label>
                            <x-input type="text" name="cpf" value="{{ old('cpf') }}" class="w-full" placeholder="000.000.000-00" />
                        </div>
                    </div>
                </x-card>

                <!-- BLOCO 2: DADOS PESSOAIS -->
                <x-card class="border-t-4 border-t-primary-500">
                    <x-slot name="header">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <x-icon name="heroicon-o-user" class="w-5 h-5 mr-2 text-primary-500" />
                            Dados Pessoais
                        </h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nome Completo <span class="text-red-500">*</span></label>
                            <x-input type="text" name="nome" value="{{ old('nome') }}" required class="w-full" placeholder="Nome completo do usuário" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Sexo</label>
                            <x-select name="sexo" class="w-full">
                                <option value="">Selecione...</option>
                                <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Feminino</option>
                            </x-select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nascimento</label>
                            <x-input type="date" name="nascimento" value="{{ old('nascimento') }}" max="{{ date('Y-m-d') }}" class="w-full" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Telefone</label>
                            <x-input type="text" name="telefone" value="{{ old('telefone') }}" oninput="this.value = this.value.replace(/[^0-9\(\)\-\+\s]/g, '')" class="w-full" placeholder="(00) 00000-0000" />
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Cidade</label>
                            <x-input type="text" name="cidade" value="{{ old('cidade') }}" class="w-full" placeholder="Nome da cidade" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Logradouro (Rua, Av.)</label>
                            <x-input type="text" name="rua" value="{{ old('rua') }}" class="w-full" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Número</label>
                            <x-input type="text" name="numero" value="{{ old('numero') }}" class="w-full" />
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Bairro</label>
                            <x-input type="text" name="bairro" value="{{ old('bairro') }}" class="w-full" />
                        </div>
                    </div>
                </x-card>

                <x-card class="border-t-4 border-t-red-500 bg-gray-50/50">
                        <x-slot name="header">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <x-icon name="heroicon-o-key" class="w-5 h-5 mr-2 text-red-500" />
                                Credenciais de Acesso
                            </h3>
                        </x-slot>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">E-mail de Acesso <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-icon name="heroicon-o-envelope" class="h-5 w-5 text-red-300" />
                                    </div>
                                    <x-input type="email" name="email" autocomplete="off" required placeholder="usuario@escola.com.br" class="pl-10 w-full !bg-white focus:!ring-red-500 focus:!border-red-500" />
                                </div>
                            </div>

                            <div x-data="{ showPass: false }">
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Senha <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-icon name="heroicon-o-lock-closed" class="h-5 w-5 text-yellow-500" />
                                    </div>
                                    <x-input x-bind:type="showPass ? 'text' : 'password'" name="password" autocomplete="new-password" required placeholder="Senha segura" class="pl-10 pr-10 w-full !bg-white focus:!ring-yellow-500 focus:!border-yellow-500" />
                                    <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                                        <template x-if="!showPass">
                                            <x-icon name="heroicon-o-eye" class="h-5 w-5" />
                                        </template>
                                        <template x-if="showPass">
                                            <x-icon name="heroicon-o-eye"-slash class="h-5 w-5" />
                                        </template>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </x-card>

                <div class="flex justify-end pt-4">
                    <x-button variant="secondary" type="button" onclick="window.location='{{ route('users.index') }}'" class="mr-3">
                        Cancelar
                    </x-button>
                    <x-button variant="primary" type="submit" class="h-11 px-8">
                        <x-icon name="heroicon-o-check"-circle class="w-5 h-5 mr-2" />
                        Cadastrar Usuário
                    </x-button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
