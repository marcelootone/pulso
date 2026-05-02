<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory;

    // Colunas que o Laravel tem permissão para preencher via formulário
    protected $fillable = [
        'modalidade',
        'turno',
        'serie',
        'complemento',
        'ativa'
    ];

    // Uma turma tem muitos alunos
    public function alunos()
    {
        return $this->hasMany(Aluno::class);
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
}