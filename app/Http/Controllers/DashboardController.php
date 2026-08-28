<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole(['Administrador', 'Gestor', 'Secretaria', 'Coordenador'])) {
            $dados = $this->dashboardService->getDadosGestor();
        } else {
            $dados = $this->dashboardService->getDadosProfessor($user);
        }

        return view('dashboard', $dados);
    }
}
