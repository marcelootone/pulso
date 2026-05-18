<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cadastro Manual de Usuário') }}
        </h2>
    </x-slot>

    <div class="py-2" x-data="{ tipoUsuario: '{{ old('tipo_usuario', \App\Models\User::TIPO_ESTUDANTE) }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- ALERTA DE DUPLICATA -->
            <div class="bg-teal-50 border border-teal-200 text-teal-800 px-4 py-2 rounded mb-2 text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <strong>ATENÇÃO:</strong>&nbsp; Sempre verifique se o usuário já existe para que não ocorra duplicatas! Para verificar, <a href="#" class="text-blue-600 hover:underline">clique aqui</a> e faça uma busca pelo nome do usuário com base no seu perfil.
            </div>

            <!-- TABS -->
            <div class="flex mb-2">
                <a href="{{ route('users.create') }}" class="bg-blue-500 text-white px-6 py-2 rounded-t-md font-semibold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Criar Usuário
                </a>
                <a href="{{ route('importar.index') }}" class="bg-white text-blue-500 border border-gray-200 border-b-0 px-6 py-2 rounded-t-md font-semibold flex items-center hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Importar Estudantes
                </a>
            </div>

            @if(session('success'))
                <div class="mb-2 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-2 bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-2 bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative" role="alert">
                    <strong class="font-bold">Opa! Algum erro ocorreu:</strong>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST" class="bg-white p-4 shadow-sm border border-gray-200">
                @csrf

                <!-- BLOCO 1: TIPO DE USUARIO E DOCUMENTOS -->
                <div class="border border-gray-200 p-2 mb-2 relative" style="border-left: 3px solid #22c55e;">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-user-circle mr-1"></i> Tipo de Usuário</label>
                            <select name="tipo_usuario" x-model="tipoUsuario" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm uppercase font-semibold text-blue-600 bg-blue-50 border-blue-200 py-1.5">
                                <option value="{{ \App\Models\User::TIPO_ESTUDANTE }}">ESTUDANTE</option>
                                <option value="{{ \App\Models\User::TIPO_PROFESSOR }}">PROFESSOR(A)</option>
                                <option value="{{ \App\Models\User::TIPO_GESTOR }}">GESTOR</option>
                                <option value="{{ \App\Models\User::TIPO_COORDENADOR }}">COORDENADOR</option>
                                <option value="{{ \App\Models\User::TIPO_SECRETARIA }}">SECRETARIA</option>
                                <option value="{{ \App\Models\User::TIPO_PROF_ESPECIAL }}">PROFESSOR EDUCAÇÃO ESPECIAL</option>
                                <option value="{{ \App\Models\User::TIPO_PROF_ESTUDO_ORIENTADO }}">PROFESSOR DE ESTUDO ORIENTADO</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-id-card mr-1"></i> Documento (CPF)</label>
                            <input type="text" name="cpf" value="{{ old('cpf') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm bg-gray-50 py-1.5">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-qrcode mr-1"></i> Número RA</label>
                            <input type="text" name="ra" value="{{ old('ra') }}" x-bind:required="tipoUsuario === '{{ \App\Models\User::TIPO_ESTUDANTE }}'" x-bind:readonly="tipoUsuario !== '{{ \App\Models\User::TIPO_ESTUDANTE }}'" :class="{'bg-gray-200': tipoUsuario !== '{{ \App\Models\User::TIPO_ESTUDANTE }}', 'bg-gray-50': tipoUsuario === '{{ \App\Models\User::TIPO_ESTUDANTE }}'}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                    </div>
                </div>

                <!-- BLOCO 2: DADOS PESSOAIS -->
                <div class="border border-gray-200 p-2 mb-2 relative" style="border-left: 3px solid #e11d48;">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-2">
                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-user mr-1"></i> Nome</label>
                            <input type="text" name="nome" value="{{ old('nome') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-venus-mars mr-1"></i> Sexo</label>
                            <select name="sexo" x-bind:required="tipoUsuario === '{{ \App\Models\User::TIPO_ESTUDANTE }}'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm text-center py-1.5">
                                <option value="">Selecione...</option>
                                <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>M</option>
                                <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>F</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-birthday-cake mr-1"></i> Nascimento</label>
                            <input type="date" name="nascimento" x-bind:required="tipoUsuario === '{{ \App\Models\User::TIPO_ESTUDANTE }}'" value="{{ old('nascimento') }}" max="{{ date('Y-m-d') }}" class="date-light mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-phone mr-1"></i> Telefone</label>
                            <input type="text" name="telefone" x-bind:required="tipoUsuario === '{{ \App\Models\User::TIPO_ESTUDANTE }}'" value="{{ old('telefone') }}" oninput="this.value = this.value.replace(/[^0-9\(\)\-\+\s]/g, '')" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-city mr-1"></i> Cidade</label>
                            <input type="text" name="cidade" value="{{ old('cidade') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm text-gray-700 py-1.5">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-6">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-road mr-1"></i> Rua</label>
                            <input type="text" name="rua" value="{{ old('rua') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-hashtag mr-1"></i> Nº</label>
                            <input type="text" name="numero" value="{{ old('numero') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-map-signs mr-1"></i> Bairro</label>
                            <input type="text" name="bairro" value="{{ old('bairro') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                    </div>
                </div>

                <!-- BLOCO 3: DADOS FILIAÇÃO (SÓ ESTUDANTE) -->
                <div x-show="tipoUsuario === '{{ \App\Models\User::TIPO_ESTUDANTE }}'" class="border border-gray-200 p-2 mb-2 relative" style="border-left: 3px solid #e11d48;" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-user mr-1"></i> Nome Pai</label>
                            <input type="text" name="nome_pai" value="{{ old('nome_pai') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-mobile-alt mr-1"></i> Telefone Pai</label>
                            <input type="text" name="tel_pai" value="{{ old('tel_pai') }}" oninput="this.value = this.value.replace(/[^0-9\(\)\-\+\s]/g, '')" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-user mr-1"></i> Nome Mãe</label>
                            <input type="text" name="nome_mae" value="{{ old('nome_mae') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase"><i class="fas fa-mobile-alt mr-1"></i> Telefone Mãe</label>
                            <input type="text" name="tel_mae" value="{{ old('tel_mae') }}" oninput="this.value = this.value.replace(/[^0-9\(\)\-\+\s]/g, '')" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-1.5">
                        </div>
                    </div>
                </div>

                <!-- BLOCO 4: VINCULAR TURMA (SÓ ESTUDANTE) -->
                <div x-show="tipoUsuario === '{{ \App\Models\User::TIPO_ESTUDANTE }}'" class="border border-gray-200 p-2 mb-2 relative" style="border-left: 3px solid #06b6d4;" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                        <div class="col-span-1">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">
                                <input type="checkbox" class="mr-1 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500"> 
                                Vincular a uma Turma?
                            </label>
                            <select name="turma_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm text-gray-600 text-center uppercase py-1.5">
                                <option value="">Selecione...</option>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                                        {{ $turma->serie }}º {{ $turma->complemento }} - {{ substr($turma->modalidade,0,2) }} ({{ substr($turma->turno,0,1) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2 text-[10px] text-blue-500 font-semibold uppercase pl-4 border-l border-gray-200 h-full flex items-center">
                            Caso opte por não vincular o estudante agora o mesmo pode ser feito posteriormente em SECRETARIA > VINCULAR ESTUDANTE.
                        </div>
                    </div>
                </div>

                <!-- BLOCO 5: USUÁRIO E SENHA + CADASTRAR -->
                <div x-show="tipoUsuario !== '{{ \App\Models\User::TIPO_ESTUDANTE }}'" class="mb-2 relative" style="border-left: 3px solid #6b7280;" x-cloak>
                    <div class="border border-gray-200 p-2 relative bg-gray-50 rounded-r-md">
                        <div class="text-[11px] font-bold text-gray-700 uppercase mb-2">
                            <i class="fas fa-key mr-1"></i> E-MAIL DE ACESSO E SENHA
                        </div>
                        
                        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                            <!-- Red Email Box -->
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </div>
                                <input type="email" name="email" autocomplete="off" x-bind:required="tipoUsuario !== '{{ \App\Models\User::TIPO_ESTUDANTE }}'" placeholder="E-mail de Acesso" class="block w-full pl-10 bg-red-600 text-white placeholder-red-200 border-none rounded-md py-2 focus:ring-2 focus:ring-red-400 focus:outline-none text-sm">
                            </div>

                            <!-- Yellow Password Box -->
                            <div class="relative w-full" x-data="{ showPass: false }">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 0 1 1 0 100-2 2 2 0 00-2 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input x-bind:type="showPass ? 'text' : 'password'" name="password" autocomplete="new-password" x-bind:required="tipoUsuario !== '{{ \App\Models\User::TIPO_ESTUDANTE }}'" placeholder="Senha" class="block w-full pl-10 pr-10 bg-yellow-400 text-gray-800 placeholder-gray-600 border-none rounded-md py-2 focus:ring-2 focus:ring-yellow-300 focus:outline-none text-sm">
                                <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-700 hover:text-black">
                                    <i class="fas" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1.5 px-6 rounded shadow flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                        </svg>
                        CADASTRAR
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Incluir FontAwesome para os ícones, se não existir -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Placeholder mais claro para o input date */
        .date-light::-webkit-datetime-edit-text, 
        .date-light::-webkit-datetime-edit-month-field, 
        .date-light::-webkit-datetime-edit-day-field, 
        .date-light::-webkit-datetime-edit-year-field {
            color: #d1d5db; /* gray-300 */
        }
        /* Cor normal quando tem valor preenchido ou está com foco */
        .date-light:focus::-webkit-datetime-edit-text, 
        .date-light:focus::-webkit-datetime-edit-month-field, 
        .date-light:focus::-webkit-datetime-edit-day-field, 
        .date-light:focus::-webkit-datetime-edit-year-field,
        .date-light:valid::-webkit-datetime-edit-text, 
        .date-light:valid::-webkit-datetime-edit-month-field, 
        .date-light:valid::-webkit-datetime-edit-day-field, 
        .date-light:valid::-webkit-datetime-edit-year-field {
            color: #1f2937; /* gray-800 */
        }
    </style>
</x-app-layout>
