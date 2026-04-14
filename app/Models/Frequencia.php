<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frequencia extends Model
{
    use HasFactory;

    protected $fillable = ['aluno_id', 'turma_id', 'user_id', 'data', 'status'];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }
}