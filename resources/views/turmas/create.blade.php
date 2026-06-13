<x-app-layout>
    <x-slot name="header">
        {{ __('Nova Turma') }}
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Acadêmico', 'url' => '#'],
            ['label' => 'Turmas', 'url' => route('turmas.index')],
            ['label' => 'Nova Turma']
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <x-card class="border-t-4 border-t-primary-600">
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Preencha os dados da nova turma</h3>
                    <a href="{{ route('turmas.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                        Voltar
                    </a>
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

            <form id="form-create-turma" action="{{ route('turmas.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Modalidade</label>
                        <x-select name="modalidade" required class="w-full">
                            <option value="">Selecione...</option>
                            <optgroup label="INFANTIL">
                                <option value="EI - Educação Infantil" {{ old('modalidade') == 'EI - Educação Infantil' ? 'selected' : '' }}>EI - Educação Infantil</option>
                            </optgroup>
                            <optgroup label="REGULAR">
                                <option value="EF - Ensino Fundamental" {{ old('modalidade') == 'EF - Ensino Fundamental' ? 'selected' : '' }}>EF - Ensino Fundamental</option>
                                <option value="EM - Ensino Médio" {{ old('modalidade') == 'EM - Ensino Médio' ? 'selected' : '' }}>EM - Ensino Médio</option>
                                <option value="EMI - Ensino Médio Integrado" {{ old('modalidade') == 'EMI - Ensino Médio Integrado' ? 'selected' : '' }}>EMI - Ensino Médio Integrado</option>
                            </optgroup>
                            <optgroup label="EJA">
                                <option value="EJA EF - Ensino de Jovens e Adultos Fundamental" {{ old('modalidade') == 'EJA EF - Ensino de Jovens e Adultos Fundamental' ? 'selected' : '' }}>EJA EF</option>
                                <option value="EJA EM - Ensino de Jovens e Adultos Médio" {{ old('modalidade') == 'EJA EM - Ensino de Jovens e Adultos Médio' ? 'selected' : '' }}>EJA EM</option>
                            </optgroup>
                        </x-select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Turno</label>
                            <x-select name="turno" required class="w-full">
                                <option value="Matutino" {{ old('turno') == 'Matutino' ? 'selected' : '' }}>Matutino</option>
                                <option value="Vespertino" {{ old('turno') == 'Vespertino' ? 'selected' : '' }}>Vespertino</option>
                                <option value="Noturno" {{ old('turno') == 'Noturno' ? 'selected' : '' }}>Noturno</option>
                                <option value="Integral" {{ old('turno') == 'Integral' ? 'selected' : '' }}>Integral</option>
                            </x-select>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Série</label>
                            <x-input type="number" name="serie" min="1" max="9" value="{{ old('serie') }}" required class="w-full" />
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Complemento</label>
                            <x-input type="text" name="complemento" maxlength="3" value="{{ old('complemento') }}" placeholder="Ex: COM" class="w-full uppercase" />
                            <p class="mt-1 text-[11px] leading-tight text-gray-500">O Complemento Consiste em nomear a turma com até 3 letras, por exemplo, 1º01 Comércio, o complemento seria COM.</p>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Ano Letivo</label>
                            <x-input type="number" name="ano_letivo" min="2000" max="2099" value="{{ old('ano_letivo', date('Y')) }}" required class="w-full" />
                        </div>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <x-button variant="secondary" type="button" onclick="window.location='{{ route('turmas.index') }}'">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit" form="form-create-turma">
                            Criar Turma
                        </x-button>
                    </div>
                </x-slot>
            </form>
        </x-card>
    </div>
</x-app-layout>