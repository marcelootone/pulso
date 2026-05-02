<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    protected $fillable = [
        'turma_id', 'ra', 'nome', 'nascimento', 'sexo', 'telefone',
        'nome_mae', 'telefone_responsavel', 'cep', 'logradouro', 'status_matricula'
    ];

    // Um aluno pertence a uma turma
    public function turma()
    {
        return $this->belongsTo(Turma::class);
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