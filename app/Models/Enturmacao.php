<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enturmacao extends Model
{
    use HasFactory;

    protected $table = 'enturmacoes';

    protected $fillable = [
        'matricula_id',
        'turma_id',
        'tipo_vinculo',
        'data_entrada',
        'data_saida',
        'status',
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
}
