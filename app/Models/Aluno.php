<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'turma_id', 'ra', 'nome', 'nascimento', 'sexo', 'telefone',
        'nome_mae', 'telefone_responsavel', 'cep', 'logradouro', 'status_matricula'
    ];

    // Um aluno pertence a um usuário (credenciais)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Um aluno pode estar matriculado em várias turmas
    public function turmas()
    {
        return $this->belongsToMany(Turma::class, 'aluno_turma')->withTimestamps();
    }
    
    public function frequencias() 
    { 
        return $this->hasMany(Frequencia::class); 
    }

    // Um aluno pode estar matriculado em várias eletivas
    public function eletivas()
    {
        return $this->belongsToMany(Eletiva::class, 'aluno_eletiva')->withTimestamps();
    }
}