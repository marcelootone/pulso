<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoAgenda extends Model
{
    protected $table = 'eventos_agenda';

    protected $fillable = ['titulo', 'descricao', 'local', 'inicio', 'fim', 'dia_inteiro', 'compartilhado'];

    protected function casts(): array
    {
        return [
            'inicio' => 'immutable_datetime',
            'fim' => 'immutable_datetime',
            'dia_inteiro' => 'boolean',
            'compartilhado' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisiveisPara(Builder $query, User $user): void
    {
        $query->where(fn (Builder $visiveis) => $visiveis
            ->where('user_id', $user->id)->orWhere('compartilhado', true));
    }
}
