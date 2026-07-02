<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Importar / Vincular Estudantes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8"
             x-data="{ tab: '{{ request('tab', 'planilha') }}' }">

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Ops! Algo deu errado:</p>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- TABS -->
            <div class="flex mb-6 border-b border-gray-200">
                <button type="button"
                    @click="tab = 'planilha'"
                    :class="tab === 'planilha'
                        ? 'text-primary-600 border-b-2 border-primary-600 font-bold'
                        : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent'"
                    class="px-6 py-3 text-sm flex items-center transition-colors focus:outline-none">
                    <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2" />
                    Importar Usuarios (Planilha)
                </button>
                <button type="button"
                    @click="tab = 'vincular'"
                    :class="tab === 'vincular'
                        ? 'text-emerald-600 border-b-2 border-emerald-600 font-bold'
                        : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent'"
                    class="px-6 py-3 text-sm flex items-center transition-colors focus:outline-none">
                    <x-heroicon-o-user-plus class="w-5 h-5 mr-2" />
                    Vincular Aluno
                </button>
            </div>

            <!-- ABA: IMPORTAR PLANILHA -->
            <div x-show="tab === 'planilha'" x-cloak>
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 shadow-sm">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Atenção:</strong> O cadastramento de <strong>ESTUDANTES</strong> em lote para a turma baseia-se no preenchimento da planilha modelo. Salve a mesma em <strong>formato CSV</strong> e carregue-a no botão abaixo.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-gray-600">
                    <form action="{{ route('importar.preview') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Destino (Turma, Eletiva ou Clube)</label>
                                <select name="destino" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Selecione o destino...</option>
                                    
                                    @if($turmas->count() > 0)
                                        <optgroup label="Turmas Regulares">
                                            @foreach($turmas as $turma)
                                                <option value="turma_{{ $turma->id }}" {{ (old('destino') ?? request('destino')) == 'turma_'.$turma->id ? 'selected' : '' }}>
                                                    {{ $turma->serie }}º {{ $turma->complemento }} - {{ $turma->modalidade }} ({{ $turma->turno }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if($eletivas->count() > 0)
                                        <optgroup label="Eletivas">
                                            @foreach($eletivas as $eletiva)
                                                <option value="eletiva_{{ $eletiva->id }}" {{ (old('destino') ?? request('destino')) == 'eletiva_'.$eletiva->id ? 'selected' : '' }}>
                                                    {{ $eletiva->nome }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if($clubes->count() > 0)
                                        <optgroup label="Clubes">
                                            @foreach($clubes as $clube)
                                                <option value="clube_{{ $clube->id }}" {{ (old('destino') ?? request('destino')) == 'clube_'.$clube->id ? 'selected' : '' }}>
                                                    {{ $clube->nome }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Arquivo CSV</label>
                                <input type="file" name="planilha" accept=".csv, .xlsx, .xls" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-md p-1" required>
                            </div>
                        </div>

                        <div class="flex justify-end mt-8">
                            <button type="submit" class="bg-green-600 text-black px-8 py-2 rounded-md hover:bg-green-700 font-bold shadow-md">
                                SALVAR
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="mt-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline text-sm">Caso você não esteja visualizando nenhuma turma, lembre-se que antes de realizar este procedimento é necessário o cadastramento das turmas.</span>
                </div>
            </div>

            <!-- ABA: VINCULAR ALUNO -->
            <div x-show="tab === 'vincular'" x-cloak>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-emerald-500"
                     x-data="{
                         search: '',
                         alunos: {{ $alunos->map(fn($a) => ['id' => (string)$a->id, 'nome' => $a->nome, 'ra' => $a->ra])->values()->toJson() }},
                         selectedIds: [],
                         get filteredAlunos() {
                             if (this.search === '') return [];
                             let s = this.search.toLowerCase();
                             return this.alunos.filter(a => a.nome.toLowerCase().includes(s) || String(a.ra).includes(s)).slice(0, 8);
                         },
                         selectAluno(aluno) {
                             if (!this.selectedIds.includes(aluno.id)) {
                                 this.selectedIds.push(aluno.id);
                             }
                             this.search = '';
                             this.$refs.searchInputVincular.focus();
                         },
                         removeAluno(id) {
                             this.selectedIds = this.selectedIds.filter(i => i !== id);
                         },
                         getSelected() {
                             return this.alunos.filter(a => this.selectedIds.includes(a.id));
                         }
                     }">
                    <form action="{{ route('vinculo.storeBulk') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Destino (Turma, Eletiva ou Clube)</label>
                                <select name="destino" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                    <option value="">Selecione o destino...</option>
                                    
                                    @if($turmas->count() > 0)
                                        <optgroup label="Turmas Regulares">
                                            @foreach($turmas as $turma)
                                                <option value="turma_{{ $turma->id }}" {{ request('destino') == 'turma_'.$turma->id ? 'selected' : '' }}>
                                                    {{ $turma->serie }}º {{ $turma->complemento }} - {{ $turma->modalidade }} ({{ $turma->turno }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if($eletivas->count() > 0)
                                        <optgroup label="Eletivas">
                                            @foreach($eletivas as $eletiva)
                                                <option value="eletiva_{{ $eletiva->id }}" {{ request('destino') == 'eletiva_'.$eletiva->id ? 'selected' : '' }}>
                                                    {{ $eletiva->nome }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if($clubes->count() > 0)
                                        <optgroup label="Clubes">
                                            @foreach($clubes as $clube)
                                                <option value="clube_{{ $clube->id }}" {{ request('destino') == 'clube_'.$clube->id ? 'selected' : '' }}>
                                                    {{ $clube->nome }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 items-start">
                            <div class="flex-grow w-full relative">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Digite os alunos a vincular</label>

                                <!-- Autocomplete Input -->
                                <input type="text"
                                       x-model="search"
                                       x-ref="searchInputVincular"
                                       placeholder="Buscar por nome ou RA..."
                                       autocomplete="off"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">

                                <!-- Dropdown -->
                                <ul x-show="search.length > 0"
                                    @click.away="search = ''"
                                    class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto mt-1"
                                    style="display: none;">
                                    <template x-for="aluno in filteredAlunos" :key="aluno.id">
                                        <li @click="selectAluno(aluno)" class="px-4 py-2 hover:bg-emerald-50 cursor-pointer text-sm">
                                            <span x-text="aluno.nome" class="font-bold text-gray-900"></span>
                                            <span class="text-gray-500 text-xs ml-1">(RA: <span x-text="aluno.ra"></span>)</span>
                                        </li>
                                    </template>
                                    <li x-show="filteredAlunos.length === 0" class="px-4 py-2 text-gray-500 text-sm italic">Nenhum aluno encontrado.</li>
                                </ul>

                                <!-- Badges de selecionados -->
                                <div class="flex flex-wrap gap-2 mt-3 mb-4" x-show="selectedIds.length > 0" style="display: none;">
                                    <template x-for="aluno in getSelected()" :key="aluno.id">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span x-text="aluno.nome"></span>
                                            <button type="button"
                                                    @click="removeAluno(aluno.id)"
                                                    class="flex-shrink-0 ml-2 inline-flex text-emerald-600 hover:bg-emerald-200 hover:text-emerald-900 rounded p-0.5 transition-colors">
                                                <x-heroicon-o-x-mark class="w-3 h-3" />
                                            </button>
                                        </span>
                                    </template>
                                </div>

                                <!-- Hidden inputs para envio -->
                                <template x-for="id in selectedIds" :key="id">
                                    <input type="hidden" name="aluno_ids[]" :value="id">
                                </template>

                                <!-- Seleção Múltipla via lista nativa -->
                                <details class="group border border-gray-200 rounded-md bg-gray-50 mt-4">
                                    <summary class="cursor-pointer font-bold px-4 py-3 text-xs text-gray-600 uppercase tracking-wider group-open:border-b border-gray-200 transition-colors">
                                        Seleção Múltipla de Alunos
                                    </summary>
                                    <div class="p-4 bg-white">
                                        <select x-model="selectedIds" multiple
                                                class="w-full border-gray-300 rounded-md shadow-sm h-48 focus:border-emerald-500 focus:ring-emerald-500">
                                            <template x-for="aluno in alunos" :key="aluno.id">
                                                <option :value="aluno.id" class="py-1 px-2 border-b">
                                                    <span x-text="aluno.nome"></span> (RA: <span x-text="aluno.ra"></span>)
                                                </option>
                                            </template>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-2 font-medium">Pressione CTRL (ou CMD) para selecionar múltiplos alunos.</p>
                                    </div>
                                </details>
                            </div>

                            <x-button variant="primary" type="submit"
                                      class="!bg-emerald-600 hover:!bg-emerald-700 w-full sm:w-auto h-11 justify-center border-none mt-7"
                                      x-bind:disabled="selectedIds.length === 0">
                                <x-heroicon-o-plus class="w-5 h-5 mr-1" /> Vincular
                            </x-button>
                        </div>
                    </form>
                </div>

                <div class="mt-6 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline text-sm">O vínculo criado aqui é do tipo <strong>REGULAR</strong>. Alunos já vinculados à turma selecionada serão ignorados automaticamente.</span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>