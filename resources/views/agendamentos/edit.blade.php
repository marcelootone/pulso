<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar reserva</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <x-card>
            <div class="mb-6 space-y-1">
                <h3 class="text-lg font-bold text-gray-900">{{ $agendamento->espaco->nome }}</h3>
                <p class="text-sm text-gray-600">Reservado por {{ $agendamento->user->name }}</p>
                <p id="periodo-reserva" class="text-sm text-gray-600">
                    Você pode agendar de {{ $dataMinima->format('d/m/Y') }} até {{ $dataMaxima->format('d/m/Y') }} (horário de Brasília).
                </p>
            </div>

            @if ($errors->any())
                <div role="alert" class="mb-6 rounded-md bg-red-50 p-4 text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('agendamentos.update', $agendamento) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="data" class="block text-sm font-bold text-gray-700 mb-1">Data da reserva</label>
                    <input id="data" name="data" type="date" required
                           value="{{ old('data', $agendamento->data) }}"
                           min="{{ $dataMinima->toDateString() }}" max="{{ $dataMaxima->toDateString() }}"
                           aria-describedby="periodo-reserva" aria-invalid="{{ $errors->has('data') ? 'true' : 'false' }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="horario_inicio" class="block text-sm font-bold text-gray-700 mb-1">Início</label>
                        <input id="horario_inicio" name="horario_inicio" type="time" required
                               value="{{ old('horario_inicio', substr($agendamento->horario_inicio, 0, 5)) }}"
                               aria-invalid="{{ $errors->has('horario_inicio') ? 'true' : 'false' }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label for="horario_fim" class="block text-sm font-bold text-gray-700 mb-1">Término</label>
                        <input id="horario_fim" name="horario_fim" type="time" required
                               value="{{ old('horario_fim', substr($agendamento->horario_fim, 0, 5)) }}"
                               aria-invalid="{{ $errors->has('horario_fim') ? 'true' : 'false' }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label for="motivo" class="block text-sm font-bold text-gray-700 mb-1">Motivo / Turma (opcional)</label>
                    <input id="motivo" name="motivo" type="text" maxlength="255"
                           value="{{ old('motivo', $agendamento->motivo) }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <x-button type="submit" variant="primary" class="justify-center">Salvar alterações</x-button>
                    <a href="{{ route('agendamentos.agenda', $agendamento->espaco_id) }}" class="rounded-md px-4 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-600">Voltar à agenda</a>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
