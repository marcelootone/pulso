<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanejamentoItem extends Model
{
    protected $table = 'planejamento_itens';

    protected $fillable = ['horario_id', 'dia_semana', 'tarefa', 'andamento', 'observacao'];

    public function horario()
    {
        return $this->belongsTo(PlanejamentoHorario::class, 'horario_id');
    }

    /**
     * Retorna o rótulo amigável do andamento.
     */
    public function getAndamentoLabelAttribute(): string
    {
        return match ($this->andamento) {
            'CONCLUIDO' => 'Concluído',
            'EM_ANDAMENTO' => 'Em Andamento',
            'NAO_CONCLUIDO' => 'Não Concluído',
            default => '',
        };
    }
}
