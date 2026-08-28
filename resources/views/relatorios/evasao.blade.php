<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Evasão</title>
    <style>
        /* Estilos focados para impressão A4 */
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; }
        .subtitle { font-size: 14px; color: #666; }

        table { w-full; border-collapse: collapse; margin-top: 20px; width: 100%; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; padding: 10px; text-align: left; border: 1px solid #ddd; }
        td { padding: 10px; border: 1px solid #ddd; }
        .danger { color: #dc2626; font-weight: bold; text-align: center; }

        .footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 50px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        .signature-box { margin-top: 50px; width: 300px; float: right; text-align: center; }
        .signature-line { border-bottom: 1px solid #000; margin-bottom: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">PULSO — Plataforma Unificada de Supervisão e Organização Educacional</div>
        <div class="subtitle">Relatório Oficial de Intervenção Pedagógica (Evasão)</div>
        <div>Data de Emissão: {{ $dataAtual }}</div>
    </div>

    <p>O presente documento lista os estudantes que, até a presente data, apresentam frequência acadêmica inferior a 75%, necessitando de intervenção imediata da equipe pedagógica segundo as diretrizes de combate à evasão.</p>

    <table>
        <thead>
            <tr>
                <th>RA</th>
                <th>Nome do Estudante</th>
                <th>Turma</th>
                <th style="text-align: center;">Frequência Atual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alunosEmRisco as $aluno)
            <tr>
                <td>{{ $aluno->ra }}</td>
                <td>{{ $aluno->nome }}</td>
                <td>{{ $aluno->serie }}º {{ $aluno->complemento }}</td>
                <td class="danger">{{ number_format($aluno->percentual, 1) }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px;">Nenhum estudante em situação de risco crítico no momento.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-box">
        <div class="signature-line"></div>
        Assinatura da Coordenação Pedagógica
    </div>

    <div class="footer">
        Gerado automaticamente pelo PULSO - Página 1
    </div>

</body>
</html>
