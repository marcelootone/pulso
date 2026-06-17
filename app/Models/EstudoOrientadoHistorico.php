<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudoOrientadoHistorico extends Model
{
    use HasFactory;

    protected $table = 'estudo_orientado_historicos';

    protected $fillable = [
        'solicitacao_id',
        'user_id',
        'acao',
        'descricao',
        'dados_anteriores',
        'dados_novos',
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
    ];

    public function solicitacao()
    {
        return $this->belongsTo(EstudoOrientadoSolicitacao::class, 'solicitacao_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
