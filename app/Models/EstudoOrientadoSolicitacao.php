<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstudoOrientadoSolicitacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'estudo_orientado_solicitacoes';

    protected $fillable = [
        'aluno_id',
        'turma_id',
        'professor_solicitante_id',
        'disciplina_solicitante',
        'motivo',
        'prioridade',
        'status',
        'coordenador_id',
        'data_analise',
        'parecer_coordenador',
        'professor_orientador_id',
        'data_atribuicao',
        'data_conclusao',
        'parecer_conclusao',
        'concluido_por_id',
    ];

    protected $casts = [
        'data_analise' => 'datetime',
        'data_atribuicao' => 'datetime',
        'data_conclusao' => 'datetime',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'professor_solicitante_id');
    }

    public function coordenador()
    {
        return $this->belongsTo(User::class, 'coordenador_id');
    }

    public function orientador()
    {
        return $this->belongsTo(User::class, 'professor_orientador_id');
    }

    public function concluidoPor()
    {
        return $this->belongsTo(User::class, 'concluido_por_id');
    }

    public function atendimentos()
    {
        return $this->hasMany(EstudoOrientadoAtendimento::class, 'solicitacao_id');
    }

    public function evolucoes()
    {
        return $this->hasMany(EstudoOrientadoEvolucao::class, 'solicitacao_id');
    }

    public function planosAcao()
    {
        return $this->hasMany(EstudoOrientadoPlanoAcao::class, 'solicitacao_id');
    }

    public function historicos()
    {
        return $this->hasMany(EstudoOrientadoHistorico::class, 'solicitacao_id');
    }

    public function scopePendente($query)
    {
        return $query->where('status', 'Pendente');
    }

    public function scopeAprovada($query)
    {
        return $query->where('status', 'Aprovada');
    }

    public function scopeRejeitada($query)
    {
        return $query->where('status', 'Rejeitada');
    }

    public function scopeEmAtendimento($query)
    {
        return $query->where('status', 'EmAtendimento');
    }

    public function scopeConcluida($query)
    {
        return $query->where('status', 'Concluida');
    }
}
