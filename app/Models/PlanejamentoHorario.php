<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanejamentoHorario extends Model
{
    protected $table = 'planejamento_horarios';

    protected $fillable = ['planejamento_id', 'ordem', 'horario_inicio', 'horario_fim'];

    public function planejamento()
    {
        return $this->belongsTo(PlanejamentoSemanal::class, 'planejamento_id');
    }

    public function itens()
    {
        return $this->hasMany(PlanejamentoItem::class, 'horario_id');
    }

    /**
     * Retorna o item de um dia específico.
     */
    public function itemDoDia(string $diaSemana): ?PlanejamentoItem
    {
        return $this->itens->firstWhere('dia_semana', $diaSemana);
    }
}
