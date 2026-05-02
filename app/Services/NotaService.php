<?php

namespace App\Services;

use App\Models\Nota;

class NotaService
{
    /**
     * Salva as notas de uma avaliação específica.
     *
     * @param array $notas Array no formato [aluno_id => valor]
     * @param int $avaliacao_id ID da avaliação
     * @return void
     */
    public function salvarNotasDaAvaliacao(array $notas, int $avaliacao_id): void
    {
        foreach ($notas as $aluno_id => $valor) {
            // A regra solicita: "salvar apenas as notas que não forem nulas"
            if ($valor !== null && $valor !== '') {
                Nota::updateOrCreate(
                    [
                        'avaliacao_id' => $avaliacao_id,
                        'aluno_id' => $aluno_id,
                    ],
                    [
                        'valor' => $valor,
                    ]
                );
            }
        }
    }
}
