<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    protected $fillable = [
        'turma_id',
        'disciplina',
        'periodo',
        'nome',
        'valor_maximo',
        'data',
    ];

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }
}
