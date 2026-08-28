<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Horários de Reserva') }}
            </h2>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <x-button variant="secondary" onclick="window.location='{{ route('agendamentos.index') }}'" class="w-full sm:w-auto justify-center">
                    <x-icon name="heroicon-o-arrow-left" class="w-5 h-5 mr-2" /> Agendamentos
                </x-button>
            </div>
        </div>
    </x-slot>

    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Módulos Adicionais', 'url' => '#'],
            ['label' => 'Horários de Reserva']
        ]" />
    </x-slot>

    <div class="max-w-5xl mx-auto">

        @if (session('success'))
            <div class="mb-6"><x-alert type="success" message="{{ session('success') }}" /></div>
        @endif
        @if (session('error'))
            <div class="mb-6"><x-alert type="error" message="{{ session('error') }}" /></div>
        @endif
        @if ($errors->any())
            <div class="mb-6"><x-alert type="error" message="{{ $errors->first() }}" /></div>
        @endif

        <x-card class="border-t-4 border-t-primary-500 mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <x-icon name="heroicon-o-clock" class="w-6 h-6 text-primary-500 mr-2" />
                    Nova faixa de horário
                </h3>
            </x-slot>

            <p class="text-sm text-gray-500 mb-4">
                Configure as faixas de horário (períodos/aulas) que ficarão disponíveis para seleção ao reservar um espaço.
            </p>

            <form action="{{ route('horarios.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome (opcional)</label>
                        <input type="text" name="nome" value="{{ old('nome') }}" placeholder="Ex: 1ª Aula"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                        <input type="time" name="horario_inicio" value="{{ old('horario_inicio') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Término</label>
                        <input type="time" name="horario_fim" value="{{ old('horario_fim') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="ativo" value="1" checked
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        Disponível para reserva
                    </label>
                    <x-button type="submit" variant="primary">
                        <x-icon name="heroicon-o-plus"-circle class="w-5 h-5 mr-2" /> Adicionar
                    </x-button>
                </div>
            </form>
        </x-card>

        <x-card class="border-t-4 border-t-primary-500">
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <x-icon name="heroicon-o-list-bullet" class="w-6 h-6 text-primary-500 mr-2" />
                    Faixas cadastradas
                </h3>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Início</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Término</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($horarios as $horario)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $horario->nome ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ \Illuminate\Support\Str::substr($horario->horario_inicio, 0, 5) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ \Illuminate\Support\Str::substr($horario->horario_fim, 0, 5) }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($horario->ativo)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ativo</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inativo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <form action="{{ route('horarios.update', $horario->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="ativo" value="{{ $horario->ativo ? 0 : 1 }}">
                                            <button type="submit" class="text-sm text-primary-600 hover:underline">
                                                {{ $horario->ativo ? 'Desativar' : 'Ativar' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('horarios.destroy', $horario->id) }}" method="POST"
                                              onsubmit="return confirm('Remover esta faixa de horário?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:underline">Remover</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                    Nenhuma faixa de horário cadastrada. Adicione a primeira acima.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-app-layout>
