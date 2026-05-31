<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudoOrientadoCumprimento extends Model
{
    use HasFactory;

    protected $table = 'estudo_orientado_cumprimentos';

    protected $fillable = [
        'atividade_id',
        'aluno_id',
        'cumpriu',
        'observacao',
    ];

    protected $casts = [
        'cumpriu' => 'boolean',
    ];

    /**
     * A atividade à qual este cumprimento pertence.
     */
    public function atividade()
    {
        return $this->belongsTo(EstudoOrientadoAtividade::class, 'atividade_id');
    }

    /**
     * O aluno que (ou não) cumpriu a atividade.
     */
    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }
}
