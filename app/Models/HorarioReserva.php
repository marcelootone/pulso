<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * RF11 - Faixa de horário configurável para a reserva de espaços.
 */
class HorarioReserva extends Model
{
    use HasFactory;

    protected $table = 'horarios_reserva';

    protected $fillable = [
        'nome',
        'horario_inicio',
        'horario_fim',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Rótulo amigável da faixa de horário. Ex.: "1ª Aula (07:00 - 07:50)".
     */
    public function getRotuloAttribute(): string
    {
        $inicio = substr((string) $this->horario_inicio, 0, 5);
        $fim = substr((string) $this->horario_fim, 0, 5);
        $intervalo = $inicio . ' - ' . $fim;

        return $this->nome ? $this->nome . ' (' . $intervalo . ')' : $intervalo;
    }
}
