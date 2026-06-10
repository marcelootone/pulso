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

            <form action="{{ route('turmas.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Modalidade</label>
                        <x-select name="modalidade" required class="w-full">
                            <option value="">Selecione...</option>
                            <optgroup label="INFANTIL">
                                <option value="EI - Educação Infantil">EI - Educação Infantil</option>
                            </optgroup>
                            <optgroup label="REGULAR">
                                <option value="EF - Ensino Fundamental">EF - Ensino Fundamental</option>
                                <option value="EM - Ensino Médio">EM - Ensino Médio</option>
                                <option value="EMI - Ensino Médio Integrado">EMI - Ensino Médio Integrado</option>
                            </optgroup>
                            <optgroup label="EJA">
                                <option value="EJA EF - Ensino de Jovens e Adultos Fundamental">EJA EF</option>
                                <option value="EJA EM - Ensino de Jovens e Adultos Médio">EJA EM</option>
                            </optgroup>
                        </x-select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Turno</label>
                            <x-select name="turno" required class="w-full">
                                <option value="Matutino">Matutino</option>
                                <option value="Vespertino">Vespertino</option>
                                <option value="Noturno">Noturno</option>
                                <option value="Integral">Integral</option>
                            </x-select>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Série</label>
                            <x-input type="number" name="serie" min="1" max="9" required class="w-full" />
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Complemento</label>
                            <x-input type="text" name="complemento" maxlength="3" placeholder="Ex: A" class="w-full uppercase" />
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 uppercase tracking-wider mb-2">Ano Letivo</label>
                            <x-input type="number" name="ano_letivo" min="2000" max="2099" value="{{ date('Y') }}" required class="w-full" />
                        </div>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <x-button variant="secondary" type="button" onclick="window.location='{{ route('turmas.index') }}'">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit">
                            Criar Turma
                        </x-button>
                    </div>
                </x-slot>
            </form>
        </x-card>
    </div>
</x-app-layout>