<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AlunoService
{
    /**
     * Retorna a lista de alunos paginada e filtrada, 
     * considerando o tipo de usuário logado.
     *
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAlunosPaginados(int $perPage = 15, ?string $search = null)
    {
        $user = Auth::user();
        $query = Aluno::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('ra', 'like', "%{$search}%");
            });
        }

        // Se for professor, filtra alunos que estão em alguma das turmas ativas vinculadas a ele
        if ($user && in_array($user->tipo_usuario, [User::TIPO_PROFESSOR, User::TIPO_PROF_ESTUDO_ORIENTADO])) {
            $turmasIds = $user->turmas()->pluck('turmas.id')->toArray();
            
            $query->whereHas('matriculas.enturmacoes', function ($q) use ($turmasIds) {
                $q->whereIn('turma_id', $turmasIds)
                  ->where('status', 'Ativo');
            });
        }

        return $query->orderBy('nome')->paginate($perPage);
    }
}
