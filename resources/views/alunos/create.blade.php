<x-app-layout>
    <x-slot name="header">Cadastrar Aluno</x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Central de Cadastros'],
            ['label' => 'Cadastrar Aluno']
        ]" />
    </x-slot>

    <div class="max-w-5xl mx-auto">
        @if ($errors->any())
            <div class="mb-6" role="alert">
                <x-alert type="error" message="Não foi possível cadastrar o aluno. Confira os campos indicados abaixo." />
            </div>
        @endif

        <x-card class="border-t-4 border-t-primary-600">
            <x-slot name="header">
                <h3 class="text-xl font-bold text-gray-900">Ficha do Aluno</h3>
                <p class="mt-2 text-sm text-gray-600">Cadastre os dados do aluno. A matrícula e o vínculo com turma são realizados nos fluxos próprios.</p>
                <p class="mt-1 text-sm text-gray-600">Os campos marcados com * são obrigatórios.</p>
            </x-slot>

            <form id="form-cadastrar-aluno" action="{{ route('alunos.store') }}" method="POST" class="space-y-8">
                @csrf

                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-4">1. Dados Pessoais</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nome" value="Nome Completo *" />
                            <x-input id="nome" name="nome" type="text" :value="old('nome')" required maxlength="255" autocomplete="name" autofocus class="mt-1 w-full" aria-describedby="nome-error" aria-invalid="{{ $errors->has('nome') ? 'true' : 'false' }}" />
                            <x-input-error id="nome-error" :messages="$errors->get('nome')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="ra" value="R.A. (Registro do Aluno) *" />
                            <x-input id="ra" name="ra" type="text" :value="old('ra')" required maxlength="255" class="mt-1 w-full" aria-describedby="ra-error" aria-invalid="{{ $errors->has('ra') ? 'true' : 'false' }}" />
                            <x-input-error id="ra-error" :messages="$errors->get('ra')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="nascimento" value="Data de Nascimento" />
                            <x-input id="nascimento" name="nascimento" type="date" :value="old('nascimento')" :max="now()->toDateString()" autocomplete="bday" class="mt-1 w-full" aria-describedby="nascimento-error" aria-invalid="{{ $errors->has('nascimento') ? 'true' : 'false' }}" />
                            <x-input-error id="nascimento-error" :messages="$errors->get('nascimento')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="sexo" value="Sexo" />
                            <x-select id="sexo" name="sexo" class="mt-1 w-full" aria-describedby="sexo-error" aria-invalid="{{ $errors->has('sexo') ? 'true' : 'false' }}">
                                <option value="">Selecione...</option>
                                <option value="M" @selected(old('sexo') === 'M')>Masculino (M)</option>
                                <option value="F" @selected(old('sexo') === 'F')>Feminino (F)</option>
                            </x-select>
                            <x-input-error id="sexo-error" :messages="$errors->get('sexo')" class="mt-2" />
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-4">2. Contato e Endereço</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="telefone" value="Telefone do Estudante" />
                            <x-input id="telefone" name="telefone" type="tel" :value="old('telefone')" maxlength="255" autocomplete="tel" class="mt-1 w-full" aria-describedby="telefone-error" aria-invalid="{{ $errors->has('telefone') ? 'true' : 'false' }}" />
                            <x-input-error id="telefone-error" :messages="$errors->get('telefone')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="telefone_responsavel" value="Telefone do Responsável" />
                            <x-input id="telefone_responsavel" name="telefone_responsavel" type="tel" :value="old('telefone_responsavel')" maxlength="255" class="mt-1 w-full" aria-describedby="telefone_responsavel-error" aria-invalid="{{ $errors->has('telefone_responsavel') ? 'true' : 'false' }}" />
                            <x-input-error id="telefone_responsavel-error" :messages="$errors->get('telefone_responsavel')" class="mt-2" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="nome_mae" value="Nome da Mãe" />
                            <x-input id="nome_mae" name="nome_mae" type="text" :value="old('nome_mae')" maxlength="255" class="mt-1 w-full" aria-describedby="nome_mae-error" aria-invalid="{{ $errors->has('nome_mae') ? 'true' : 'false' }}" />
                            <x-input-error id="nome_mae-error" :messages="$errors->get('nome_mae')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="cep" value="CEP" />
                            <x-input id="cep" name="cep" type="text" :value="old('cep')" maxlength="9" autocomplete="postal-code" placeholder="00000-000" class="mt-1 w-full" aria-describedby="cep-error" aria-invalid="{{ $errors->has('cep') ? 'true' : 'false' }}" />
                            <x-input-error id="cep-error" :messages="$errors->get('cep')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="logradouro" value="Logradouro" />
                            <x-input id="logradouro" name="logradouro" type="text" :value="old('logradouro')" maxlength="255" autocomplete="street-address" class="mt-1 w-full" aria-describedby="logradouro-error" aria-invalid="{{ $errors->has('logradouro') ? 'true' : 'false' }}" />
                            <x-input-error id="logradouro-error" :messages="$errors->get('logradouro')" class="mt-2" />
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-4">3. Situação do Aluno</legend>
                    <x-input-label for="status_matricula" value="Status do Aluno *" />
                    <x-select id="status_matricula" name="status_matricula" required class="mt-1 w-full md:w-1/2" aria-describedby="status_matricula-error" aria-invalid="{{ $errors->has('status_matricula') ? 'true' : 'false' }}">
                        @foreach (['Ativo', 'Novato', 'Transferência', 'Deixou de frequentar', 'Falecimento'] as $status)
                            <option value="{{ $status }}" @selected(old('status_matricula', 'Novato') === $status)>{{ $status }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error id="status_matricula-error" :messages="$errors->get('status_matricula')" class="mt-2" />
                </fieldset>
            </form>

            <x-slot name="footer">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
                    <x-button variant="secondary" onclick="window.location='{{ route('dashboard') }}'">Cancelar</x-button>
                    <x-button variant="primary" type="submit" form="form-cadastrar-aluno">
                        <x-icon name="heroicon-o-check" class="w-4 h-4 mr-2" /> Cadastrar Aluno
                    </x-button>
                </div>
            </x-slot>
        </x-card>
    </div>
</x-app-layout>
