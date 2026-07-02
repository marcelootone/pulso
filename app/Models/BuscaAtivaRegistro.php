<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuscaAtivaRegistro extends Model
{
    use HasFactory;

    protected $table = 'busca_ativa_registros';

    protected $fillable = [
        'aluno_id',
        'matricula_id',
        'user_id',
        'observacao',
        'data'
    ];

    /**
     * O aluno que está sendo acompanhado.
     */
    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    /**
     * A matrícula do estudante monitorado (conforme DER do TCC).
     */
    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    /**
     * O usuário (coordenador/gestor) que fez o registro.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
