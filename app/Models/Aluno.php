<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'ra', 'nome', 'nascimento', 'sexo', 'telefone',
        'nome_mae', 'telefone_responsavel', 'cep', 'logradouro', 'status_matricula'
    ];

    // Um aluno pertence a um usuário (credenciais)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }
    
    public function frequencias() 
    { 
        return $this->hasMany(Frequencia::class); 
    }
}