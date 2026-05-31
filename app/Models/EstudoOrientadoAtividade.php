<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudoOrientadoAtividade extends Model
{
    use HasFactory;

    protected $table = 'estudo_orientado_atividades';

    protected $fillable = [
        'turma_id',
        'professor_solicitante_id',
        'disciplina_solicitante',
        'data_prevista',
        'descricao',
        'status',
    ];

    protected $casts = [
        'data_prevista' => 'date',
    ];

    /**
     * A turma à qual esta atividade pertence.
     */
    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    /**
     * O professor que solicitou a atividade.
     */
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'professor_solicitante_id');
    }

    /**
     * Os registros de cumprimento dos alunos para esta atividade.
     */
    public function cumprimentos()
    {
        return $this->hasMany(EstudoOrientadoCumprimento::class, 'atividade_id');
    }

    /**
     * Scope para atividades pendentes.
     */
    public function scopePendente($query)
    {
        return $query->where('status', 'Pendente');
    }

    /**
     * Scope para atividades já avaliadas.
     */
    public function scopeAvaliada($query)
    {
        return $query->where('status', 'Avaliada');
    }
}
