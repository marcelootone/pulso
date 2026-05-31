<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrequenciaEletiva extends Model
{
    protected $table = 'frequencia_eletivas';

    protected $fillable = [
        'aluno_id',
        'eletiva_id',
        'user_id',
        'data',
        'status',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function eletiva()
    {
        return $this->belongsTo(Eletiva::class);
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
