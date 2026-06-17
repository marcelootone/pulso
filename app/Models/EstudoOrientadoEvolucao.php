<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstudoOrientadoEvolucao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'estudo_orientado_evolucoes';

    protected $fillable = [
        'solicitacao_id',
        'professor_orientador_id',
        'data_registro',
        'descricao',
        'indicador',
    ];

    protected $casts = [
        'data_registro' => 'date',
    ];

    public function solicitacao()
    {
        return $this->belongsTo(EstudoOrientadoSolicitacao::class, 'solicitacao_id');
    }

    public function orientador()
    {
        return $this->belongsTo(User::class, 'professor_orientador_id');
    }
}
