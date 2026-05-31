<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaEletiva extends Model
{
    protected $table = 'notas_eletivas';

    protected $fillable = [
        'aluno_id',
        'eletiva_id',
        'descricao',
        'nota',
        'data',
    ];

    protected $casts = [
        'data' => 'date',
        'nota' => 'decimal:2',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function eletiva()
    {
        return $this->belongsTo(Eletiva::class);
    }
}
