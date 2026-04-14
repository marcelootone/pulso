<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    protected $fillable = ['turma_id', 'ra', 'nome', 'nascimento', 'sexo', 'telefone'];

    // Um aluno pertence a uma turma
    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
    
    public function frequencias() 
    { 
        return $this->hasMany(Frequencia::class); 
    }
}