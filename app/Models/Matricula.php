<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    protected $fillable = [
        'aluno_id',
        'ano_letivo',
        'etapa',
        'status',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function enturmacoes()
    {
        return $this->hasMany(Enturmacao::class);
    }
}
