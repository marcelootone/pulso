<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Espaco extends Model
{
    protected $fillable = ['nome', 'capacidade', 'status'];

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }
}
