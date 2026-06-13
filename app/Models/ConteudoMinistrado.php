<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConteudoMinistrado extends Model
{
    protected $fillable = [
        'turma_id',
        'user_id',
        'disciplina',
        'data',
        'aula_numero',
        'descricao',
    ];

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
