<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanejamentoSemanal extends Model
{
    protected $table = 'planejamento_semanal';

    protected $fillable = ['user_id', 'semana_inicio', 'semana_fim'];

    protected function casts(): array
    {
        return [
            'semana_inicio' => 'date',
            'semana_fim' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function horarios()
    {
        return $this->hasMany(PlanejamentoHorario::class, 'planejamento_id')->orderBy('ordem');
    }
}
