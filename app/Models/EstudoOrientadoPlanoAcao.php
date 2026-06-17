<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstudoOrientadoPlanoAcao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'estudo_orientado_planos_acao';

    protected $fillable = [
        'solicitacao_id',
        'professor_orientador_id',
        'descricao',
        'metas',
        'estrategias',
        'prazo',
        'status',
    ];

    protected $casts = [
        'prazo' => 'date',
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
