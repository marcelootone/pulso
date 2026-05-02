<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reserva: {{ $espaco->nome }}
            </h2>
            <a href="{{ route('agendamentos.index') }}" class="text-sm text-blue-600 hover:underline font-bold">⬅ Voltar aos Espaços</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alertas -->
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Atenção</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Sucesso</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filtro de Data -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center gap-4">
                <label class="font-bold text-gray-700">Data da Reserva:</label>
                <input type="date" value="{{ $dataSelecionada }}" 
                       onchange="window.location.href='{{ route('agendamentos.create', $espaco->id) }}?data=' + this.value"
                       class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-bold text-gray-800">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Horários já reservados -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Ocupação em {{ \Carbon\Carbon::parse($dataSelecionada)->format('d/m/Y') }}
                    </h3>
                    
                    @if($agendamentos->isEmpty())
                        <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <p class="text-gray-500">Nenhum horário reservado para este dia.</p>
                            <p class="text-sm font-bold text-green-600 mt-1">Sala 100% Livre!</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($agendamentos as $agendamento)
                                <div class="p-3 bg-gray-50 border-l-4 border-blue-500 rounded flex flex-col">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-bold text-gray-800 text-lg">
                                            {{ \Carbon\Carbon::parse($agendamento->horario_inicio)->format('H:i') }} às {{ \Carbon\Carbon::parse($agendamento->horario_fim)->format('H:i') }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ $agendamento->user->name }}
                                    </span>
                                    @if($agendamento->motivo)
                                        <span class="text-xs text-gray-500 mt-1 italic">Motivo: {{ $agendamento->motivo }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Formulário de Reserva -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Efetuar Nova Reserva</h3>
                    
                    <form action="{{ route('agendamentos.store', $espaco->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="data" value="{{ $dataSelecionada }}">

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Início</label>
                                <input type="time" name="horario_inicio" value="{{ old('horario_inicio') }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Término</label>
                                <input type="time" name="horario_fim" value="{{ old('horario_fim') }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Motivo / Turma (Opcional)</label>
                            <input type="text" name="motivo" value="{{ old('motivo') }}" placeholder="Ex: Aula prática com o 1º A"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            SALVAR RESERVA
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
