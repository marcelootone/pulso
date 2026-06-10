<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService
    ) {}

    /**
     * Exibe o formulário de solicitação de recuperação de senha.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Processa a solicitação de envio do link de recuperação de senha.
     *
     * Proteção contra user enumeration: a mensagem de sucesso é sempre a mesma,
     * independente de o e-mail existir ou não na base de dados.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email:rfc,dns'],
        ], [
            'email.required' => 'Informe seu endereço de e-mail.',
            'email.email'    => 'O endereço de e-mail informado é inválido.',
        ]);

        $status = $this->passwordResetService->sendResetLink($request->string('email')->toString());

        // Independente do status (enviado ou e-mail não encontrado), exibimos a mesma
        // mensagem para evitar enumeração de usuários cadastrados.
        if ($status === Password::RESET_LINK_SENT || $status === Password::INVALID_USER) {
            return back()->with('status', __('passwords.sent'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
