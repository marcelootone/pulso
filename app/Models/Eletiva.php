<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eletiva extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'descricao', 'vagas', 'user_id'];

    // Uma eletiva tem um professor responsável
    public function professor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Uma eletiva tem vários alunos matriculados
    public function alunos()
    {
        return $this->belongsToMany(Aluno::class, 'aluno_eletiva')->withTimestamps();
    }
}