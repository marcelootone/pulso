<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Frequência Mensal - Turma {{ $turma->serie }}º {{ $turma->complemento }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #1a56db; }
        .header p { margin: 5px 0 0 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px; text-align: center; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .col-nome { text-align: left; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .status-p { color: #16a34a; font-weight: bold; }
        .status-f { color: #dc2626; font-weight: bold; }
        .status-fj { color: #d97706; font-weight: bold; }
        .footer { text-align: right; margin-top: 30px; font-size: 9px; color: #777; }
        .summary { background-color: #e5e7eb; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Relatório de Frequência Mensal</h1>
        <p>
            <strong>Turma:</strong> {{ $turma->serie }}º {{ $turma->complemento }} - {{ $turma->modalidade }} ({{ $turma->turno }}) |
            <strong>Mês de Referência:</strong> {{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}/{{ $ano }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">Nº</th>
                <th rowspan="2" class="col-nome">Nome do Aluno</th>
                @if(count($dias_letivos) > 0)
                    <th colspan="{{ count($dias_letivos) }}">Dias Letivos do Mês</th>
                @else
                    <th>Dias Letivos</th>
                @endif
                <th colspan="3" class="summary">Resumo</th>
                <th rowspan="2" class="summary">% Pres.</th>
            </tr>
            <tr>
                @forelse($dias_letivos as $sessao)
                    <th style="min-width: 35px; font-size: 8px; padding: 2px;" title="{{ $sessao['data'] }} - {{ $sessao['professor_nome'] }}">
                        {{ \Carbon\Carbon::parse($sessao['data'])->format('d') }}<br>
                        <span style="font-size: 8px; color: #555; display: block; overflow: hidden; text-overflow: ellipsis; max-width: 45px; margin: 0 auto;">{{ $sessao['professor_nome'] }}</span>
                    </th>
                @empty
                    <th>-</th>
                @endforelse
                <th class="summary" style="width: 20px;" title="Presenças">P</th>
                <th class="summary" style="width: 20px;" title="Faltas">F</th>
                <th class="summary" style="width: 20px;" title="Faltas Justificadas">FJ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alunos as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="col-nome">{{ $row['aluno']->nome }}</td>
                    @forelse($dias_letivos as $sessao)
                        @php
                            $key = $sessao['data'] . '_' . $sessao['user_id'];
                            $status = $row['dias'][$key] ?? '-';
                        @endphp
                        <td class="
                            {{ $status === 'P' ? 'status-p' : '' }}
                            {{ $status === 'F' ? 'status-f' : '' }}
                            {{ $status === 'FJ' ? 'status-fj' : '' }}
                        ">{{ $status }}</td>
                    @empty
                        <td>Nenhum registro no mês.</td>
                    @endforelse

                    <td class="summary">{{ $row['total_presencas'] }}</td>
                    <td class="summary status-f">{{ $row['total_faltas'] }}</td>
                    <td class="summary">{{ $row['total_faltas_justificadas'] }}</td>
                    <td class="summary {{ $row['percentual'] < 75 ? 'status-f' : '' }}">
                        {{ $row['percentual'] }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($dias_letivos) + 6 }}">Nenhum aluno enturmado nesta turma.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Gerado pelo PULSO em {{ $dataEmissao }}
    </div>

</body>
</html>
