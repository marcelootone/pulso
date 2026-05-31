<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eletiva extends Model
{
    protected $table = 'eletivas';

    protected $fillable = [
        'nome',
        'descricao',
        'tipo',
        'vagas',
        'usa_nota',
        'ativa',
        'ano_letivo',
    ];

    protected $casts = [
        'usa_nota' => 'boolean',
        'ativa' => 'boolean',
        'ano_letivo' => 'integer',
        'vagas' => 'integer',
    ];

    public function professores()
    {
        return $this->belongsToMany(User::class, 'eletiva_professor', 'eletiva_id', 'user_id')
                    ->withTimestamps();
    }

    public function alunos()
    {
        return $this->belongsToMany(Aluno::class, 'aluno_eletiva', 'eletiva_id', 'aluno_id')
                    ->withPivot('data_inscricao', 'data_saida', 'status')
                    ->withTimestamps();
    }

    public function frequencias()
    {
        return $this->hasMany(FrequenciaEletiva::class, 'eletiva_id');
    }

    public function notas()
    {
        return $this->hasMany(NotaEletiva::class, 'eletiva_id');
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativa', true);
    }

    public function alunosAtivos()
    {
        return $this->alunos()->wherePivot('status', 'Ativo');
    }
}
