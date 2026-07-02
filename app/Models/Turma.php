<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory;

    // Colunas que o Laravel tem permissão para preencher via formulário
    protected $fillable = [
        'ano_letivo',
        'tipo',
        'modalidade',
        'turno',
        'serie',
        'complemento',
        'ativa'
    ];

    /**
     * Nome composto e legível da turma (a tabela não possui coluna "nome").
     * Ex.: "1º COM - EM - Ensino Médio (Matutino)".
     */
    public function getNomeAttribute(): string
    {
        $partes = trim(($this->serie ? $this->serie . 'º' : '') . ' ' . ($this->complemento ?? ''));
        $nome = $partes !== '' ? $partes : 'Turma';

        if (!empty($this->modalidade)) {
            $nome .= ' - ' . $this->modalidade;
        }
        if (!empty($this->turno)) {
            $nome .= ' (' . ucfirst($this->turno) . ')';
        }

        return $nome;
    }

    public function enturmacoes()
    {
        return $this->hasMany(Enturmacao::class);
    }

    // Uma turma tem muitos professores
    public function professores()
    {
        return $this->belongsToMany(User::class, 'professor_turma', 'turma_id', 'user_id')
                    ->withPivot('disciplina')
                    ->withTimestamps();
    }

    // Uma turma tem muitas avaliações
    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }

    // Uma turma tem muitas solicitações de Estudo Orientado
    public function solicitacoesEstudoOrientado()
    {
        return $this->hasMany(EstudoOrientadoSolicitacao::class);
    }
}