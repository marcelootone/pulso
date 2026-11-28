<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agenda do {{ $espaco->tipo === 'equipamento' ? 'equipamento' : 'espaço' }}</h2>
            <a href="{{ route('agendamentos.index') }}" class="text-sm font-semibold text-primary-700 hover:underline rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-600">Voltar aos espaços e equipamentos</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        @if (session('success'))
            <div role="status"><x-alert type="success" :message="session('success')" /></div>
        @endif

        <section aria-labelledby="nome-recurso" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 p-5 sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-primary-600">Agendamento de espaços e equipamentos</p>
                <h3 id="nome-recurso" class="mt-1 text-2xl font-bold text-gray-900">{{ $espaco->nome }}</h3>
                <p class="mt-2 text-sm text-gray-500">Consulte as reservas por dia. Horários de Brasília.</p>
            </div>

            <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h4 class="font-semibold text-gray-900">{{ $historico ? 'Histórico de reservas' : 'Próximas reservas' }}</h4>
                    <p class="mt-1 text-sm text-gray-500">{{ $agendamentos->total() }} {{ $agendamentos->total() === 1 ? 'reserva' : 'reservas' }} · {{ $historico ? 'Encerradas' : 'Futuras e em andamento' }}</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('agendamentos.agenda', ['espaco' => $espaco, 'historico' => $historico ? 0 : 1]) }}"
                       class="inline-flex items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-600">
                        <x-icon name="heroicon-o-clock" class="h-4 w-4" />
                        {{ $historico ? 'Ver próximas reservas' : 'Exibir histórico' }}
                    </a>
                    <a href="{{ route('agendamentos.create', $espaco) }}"
                       class="inline-flex items-center justify-center gap-2 rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                        <x-icon name="heroicon-o-plus" class="h-4 w-4" /> Agendar
                    </a>
                </div>
            </div>

            @if ($agendamentos->isEmpty())
                <div class="border-t border-gray-100 px-6 py-12 text-center">
                    <x-icon name="heroicon-o-calendar-days" class="mx-auto h-10 w-10 text-gray-400" />
                    <p class="mt-3 font-semibold text-gray-700">{{ $historico ? 'Nenhuma reserva encerrada para este recurso.' : 'Nenhum agendamento futuro ou em andamento para este recurso.' }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $historico ? 'As reservas aparecerão aqui após o término.' : 'Use o botão Agendar para fazer uma reserva.' }}</p>
                </div>
            @else
                <table class="block w-full md:table text-left">
                    <caption class="sr-only">{{ $historico ? 'Histórico' : 'Agenda' }} de {{ $espaco->nome }}, agrupado por dia</caption>
                    <thead class="hidden md:table-header-group bg-gray-800 text-white">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide">Dia</th>
                            <th scope="col" class="px-4 py-3 text-xs font-semibold uppercase tracking-wide">Início / Fim</th>
                            <th scope="col" class="px-4 py-3 text-xs font-semibold uppercase tracking-wide">Agendado por</th>
                            <th scope="col" class="px-4 py-3 text-xs font-semibold uppercase tracking-wide">Informações</th>
                            <th scope="col" class="px-4 py-3 text-xs font-semibold uppercase tracking-wide">Ações</th>
                        </tr>
                    </thead>
                    @foreach ($reservasPorDia as $data => $reservas)
                        @php
                            $dia = \Carbon\CarbonImmutable::parse($data)->locale('pt_BR');
                            $hoje = $data === $agora->toDateString();
                        @endphp
                        <tbody class="block border-t border-gray-200 md:table-row-group">
                            <tr class="block bg-primary-50 px-5 py-3 md:hidden">
                                <th colspan="5" scope="rowgroup" class="block text-sm font-semibold text-primary-800">
                                    <time datetime="{{ $data }}">{{ $dia->translatedFormat('l, d \d\e F \d\e Y') }}</time>
                                    @if ($hoje)<span class="ml-2 text-xs">Hoje</span>@endif
                                </th>
                            </tr>
                            @foreach ($reservas as $reserva)
                                <tr class="block border-b border-gray-100 p-5 last:border-b-0 md:table-row md:p-0 hover:bg-gray-50">
                                    @if ($loop->first)
                                        <th scope="rowgroup" rowspan="{{ $reservas->count() }}" class="hidden w-40 border-r border-gray-200 bg-gray-50 px-5 py-5 text-center align-top md:table-cell">
                                            <time datetime="{{ $data }}">
                                                <span class="block text-xs font-semibold uppercase text-gray-500">{{ $dia->translatedFormat('l') }}</span>
                                                <span class="mt-1 block text-4xl font-bold {{ $hoje ? 'text-primary-700' : 'text-gray-700' }}">{{ $dia->format('d') }}</span>
                                                <span class="mt-1 block text-xs uppercase text-gray-500">{{ $dia->translatedFormat('F Y') }}</span>
                                            </time>
                                            @if ($hoje)<span class="mt-2 inline-block rounded bg-primary-100 px-2 py-1 text-xs font-semibold text-primary-800">Hoje</span>@endif
                                        </th>
                                    @endif
                                    <td class="block md:table-cell md:px-4 md:py-4 align-top">
                                        <span class="whitespace-nowrap text-sm font-bold text-gray-900">{{ substr($reserva->horario_inicio, 0, 5) }} às {{ substr($reserva->horario_fim, 0, 5) }}</span>
                                        @if (! $historico && $hoje && $reserva->horario_inicio <= $agora->format('H:i:s'))
                                            <span class="mt-1 block text-xs font-semibold text-primary-700">Em andamento</span>
                                        @endif
                                    </td>
                                    <td class="mt-2 block text-sm text-gray-700 md:mt-0 md:table-cell md:px-4 md:py-4 align-top break-words">
                                        <span class="font-semibold md:hidden">Agendado por: </span>{{ $reserva->user?->name ?? 'Usuário indisponível' }}
                                    </td>
                                    <td class="mt-2 block text-sm text-gray-600 md:mt-0 md:table-cell md:px-4 md:py-4 align-top break-words">
                                        <span class="font-semibold md:hidden">Informações: </span>{{ $reserva->motivo ?: 'Sem informações adicionais' }}
                                    </td>
                                    <td class="block md:table-cell md:px-4 md:py-4 align-top">
                                        @can('update', $reserva)
                                            <a href="{{ route('agendamentos.edit', $reserva) }}" aria-label="Editar reserva de {{ $dia->format('d/m/Y') }} às {{ substr($reserva->horario_inicio, 0, 5) }}"
                                               class="mt-3 inline-flex items-center gap-1 rounded-md bg-primary-50 px-3 py-2 text-sm font-semibold text-primary-700 hover:bg-primary-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-600 md:mt-0">
                                                <x-icon name="heroicon-o-pencil-square" class="h-4 w-4" /> Editar
                                            </a>
                                        @else
                                            <span class="hidden text-gray-400 md:inline" aria-label="Sem ações disponíveis">—</span>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach
                </table>
                @if ($agendamentos->hasPages())
                    <div class="border-t border-gray-200 p-5">{{ $agendamentos->links() }}</div>
                @endif
            @endif
        </section>
    </div>
</x-app-layout>
