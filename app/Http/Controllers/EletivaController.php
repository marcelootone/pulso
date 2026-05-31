<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EletivaController extends Controller
{
    protected $eletivaService;

    public function __construct(\App\Services\EletivaService $eletivaService)
    {
        $this->eletivaService = $eletivaService;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Eletiva::with('professores')->withCount('alunos');
        
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->hasRole(['Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado']) && !$user->hasRole(['Gestor', 'Secretaria', 'Coordenador'])) {
            $query->whereHas('professores', function($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $eletivas = $query->orderBy('nome')->paginate(15);
        return view('eletivas.index', compact('eletivas'));
    }

    public function create()
    {
        $professores = \App\Models\User::role(['Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado'])
                        ->where('ativo', true)
                        ->orderBy('name')
                        ->get();
        return view('eletivas.create', compact('professores'));
    }

    public function store(\App\Http\Requests\EletivaRequest $request)
    {
        $this->eletivaService->criarEletiva($request->validated());
        return redirect()->route('eletivas.index')->with('success', 'Registro criado com sucesso!');
    }

    public function show(\App\Models\Eletiva $eletiva)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->hasRole(['Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado']) && !$user->hasRole(['Gestor', 'Secretaria', 'Coordenador'])) {
            if (!$eletiva->professores->contains($user->id)) {
                abort(403, 'Acesso não autorizado a esta eletiva/clube.');
            }
        }

        $eletiva->load(['professores', 'alunos' => function($q) {
            $q->orderBy('nome');
        }]);

        $alunosParaInscricao = \App\Models\Aluno::where('status_matricula', 'Ativo')
            ->whereDoesntHave('eletivas', function($q) use ($eletiva) {
                $q->where('eletivas.id', $eletiva->id);
            })->orderBy('nome')->get();

        return view('eletivas.show', compact('eletiva', 'alunosParaInscricao'));
    }

    public function edit(\App\Models\Eletiva $eletiva)
    {
        $professores = \App\Models\User::role(['Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado'])
                        ->where('ativo', true)
                        ->orderBy('name')
                        ->get();
        return view('eletivas.edit', compact('eletiva', 'professores'));
    }

    public function update(\App\Http\Requests\EletivaRequest $request, \App\Models\Eletiva $eletiva)
    {
        $this->eletivaService->atualizarEletiva($eletiva, $request->validated());
        return redirect()->route('eletivas.index')->with('success', 'Registro atualizado com sucesso!');
    }

    public function destroy(\App\Models\Eletiva $eletiva)
    {
        $this->eletivaService->toggleAtiva($eletiva);
        $status = $eletiva->ativa ? 'ativado' : 'desativado';
        return back()->with('success', "Registro $status com sucesso!");
    }
}
