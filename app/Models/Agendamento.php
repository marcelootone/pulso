<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $fillable = ['espaco_id', 'user_id', 'data', 'horario_inicio', 'horario_fim', 'motivo'];

    public function espaco()
    {
        return $this->belongsTo(Espaco::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
