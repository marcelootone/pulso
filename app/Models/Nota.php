<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $fillable = [
        'avaliacao_id',
        'aluno_id',
        'valor',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function avaliacao()
    {
        return $this->belongsTo(Avaliacao::class);
    }
}
