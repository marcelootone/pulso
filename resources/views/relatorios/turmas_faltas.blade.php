<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ranking de Faltas - {{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}/{{ $ano }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 22px; color: #ea580c; }
        .header p { margin: 5px 0 0 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f3f4f6; font-weight: bold; font-size: 13px; }
        .col-turma { text-align: left; font-weight: bold; font-size: 14px; }
        .footer { text-align: right; margin-top: 40px; font-size: 10px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
        .risco-alto { color: #dc2626; font-weight: bold; }
        .risco-medio { color: #d97706; font-weight: bold; }
        .risco-baixo { color: #16a34a; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Ranking de Turmas com Mais Faltas</h1>
        <p>
            <strong>Mês de Referência:</strong> {{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}/{{ $ano }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">Posição</th>
                <th class="col-turma">Turma</th>
                <th>Total de Registros</th>
                <th>Total de Faltas (F)</th>
                <th>Índice de Ausência (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ranking as $index => $dado)
                <tr>
                    <td style="font-weight: bold; font-size: 14px;">{{ $index + 1 }}º</td>
                    <td class="col-turma">
                        {{ $dado->turma->serie }}º {{ $dado->turma->complemento }}
                        <span style="font-weight: normal; font-size: 12px; color: #666;">
                            - {{ $dado->turma->modalidade }} ({{ $dado->turma->turno }})
                        </span>
                    </td>
                    <td>{{ $dado->total_registros }}</td>
                    <td>{{ $dado->total_faltas }}</td>
                    <td class="
                        {{ $dado->percentual_ausencia >= 25 ? 'risco-alto' : '' }}
                        {{ $dado->percentual_ausencia >= 15 && $dado->percentual_ausencia < 25 ? 'risco-medio' : '' }}
                        {{ $dado->percentual_ausencia < 15 ? 'risco-baixo' : '' }}
                    " style="font-size: 14px;">
                        {{ $dado->percentual_ausencia }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 20px; font-style: italic; color: #666;">
                        Não há dados de frequência registrados para o mês de {{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}/{{ $ano }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 11px; color: #555;">
        <p><strong>Legenda do Índice de Ausência:</strong></p>
        <ul style="list-style: none; padding-left: 0;">
            <li><span class="risco-alto">■ Vermelho</span>: Acima de 25% de ausência (Crítico - Abaixo da meta de 75% de presença)</li>
            <li><span class="risco-medio">■ Laranja</span>: Entre 15% e 25% de ausência (Atenção)</li>
            <li><span class="risco-baixo">■ Verde</span>: Abaixo de 15% de ausência (Adequado)</li>
        </ul>
    </div>

    <div class="footer">
        Documento Oficial PULSO - Gerado em {{ $dataEmissao }}
    </div>

</body>
</html>
