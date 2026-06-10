<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService
    ) {}

    /**
     * Exibe o formulário de redefinição de senha.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Processa a redefinição da senha.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email:rfc'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'token.required'         => 'Token de redefinição inválido.',
            'email.required'         => 'Informe seu e-mail.',
            'email.email'            => 'O e-mail informado é inválido.',
            'password.required'      => 'Defina uma nova senha.',
            'password.confirmed'     => 'As senhas não coincidem.',
        ]);

        $status = $this->passwordResetService->resetPassword(
            $request->only('email', 'password', 'password_confirmation', 'token')
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }
}
