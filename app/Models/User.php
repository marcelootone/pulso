<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// --- A MUDANÇA ACONTECE AQUI NESTA LINHA ---
// Adicionamos o 'ra' e o 'tipo_usuario' dentro do Atributo Fillable
#[Fillable(['ra', 'name', 'email', 'password', 'tipo_usuario', 'username', 'cpf', 'nascimento', 'sexo', 'telefone', 'cidade', 'rua', 'numero', 'bairro'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    const TIPO_ESTUDANTE = 'Estudante';
    const TIPO_PROFESSOR = 'Professor';
    const TIPO_GESTOR = 'Gestor';
    const TIPO_COORDENADOR = 'Coordenador';
    const TIPO_SECRETARIA = 'Secretaria';
    const TIPO_PROF_ESPECIAL = 'Professor Educação Especial';
    const TIPO_PROF_ESTUDO_ORIENTADO = 'Professor de Estudo Orientado';
    const TIPO_ADMINISTRADOR = 'Administrador';
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Um usuário pode ser um aluno
    public function aluno()
    {
        return $this->hasOne(Aluno::class);
    }

    // Um professor tem muitas turmas
    public function turmas()
    {
        return $this->belongsToMany(Turma::class, 'professor_turma')
                    ->withPivot('disciplina')
                    ->withTimestamps();
    }

    // Um usuário tem muitos planejamentos semanais
    public function planejamentos()
    {
        return $this->hasMany(PlanejamentoSemanal::class);
    }

    public function eletivas()
    {
        return $this->belongsToMany(Eletiva::class, 'eletiva_professor', 'user_id', 'eletiva_id')
                    ->withTimestamps();
    }

    // Solicitações de Estudo Orientado feitas por este professor
    public function solicitacoesEstudoOrientado()
    {
        return $this->hasMany(EstudoOrientadoSolicitacao::class, 'professor_solicitante_id');
    }

    // Acompanhamentos de Estudo Orientado atribuídos a este professor orientador
    public function acompanhamentosEstudoOrientado()
    {
        return $this->hasMany(EstudoOrientadoSolicitacao::class, 'professor_orientador_id');
    }

    /**
     * Envia a notificação de redefinição de senha customizada em português.
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}