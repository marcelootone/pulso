<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Criar nova senha</h2>
        <p class="text-sm text-gray-500 mt-1">
            Escolha uma senha forte para proteger sua conta.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5" id="reset-password-form" novalidate>
        @csrf

        {{-- Token oculto --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- E-mail --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="seu@email.com"
                    class="block w-full pl-10 pr-3 py-2.5 border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }} rounded-lg focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Nova senha --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nova senha</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres"
                    class="block w-full pl-10 pr-10 py-2.5 border {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }} rounded-lg focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                    oninput="checkPasswordStrength(this.value)"
                />
                <button type="button" onclick="togglePassword('password', 'eye-password')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                    <svg id="eye-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>

            {{-- Indicador de força --}}
            <div class="mt-2" id="strength-container" style="display:none">
                <div class="flex gap-1 mb-1">
                    <div id="bar-1" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors"></div>
                    <div id="bar-2" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors"></div>
                    <div id="bar-3" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors"></div>
                    <div id="bar-4" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors"></div>
                </div>
                <p id="strength-label" class="text-xs text-gray-500"></p>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmar senha --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar nova senha</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repita a senha"
                    class="block w-full pl-10 pr-10 py-2.5 border {{ $errors->has('password_confirmation') ? 'border-red-400' : 'border-gray-300' }} rounded-lg focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                />
                <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                    <svg id="eye-confirm" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Botão --}}
        <div class="pt-1">
            <button
                id="reset-submit-btn"
                type="submit"
                class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>
                Redefinir senha
            </button>
        </div>
    </form>

    <script>
        function togglePassword(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye   = document.getElementById(eyeId);
            input.type  = input.type === 'password' ? 'text' : 'password';
            eye.style.opacity = input.type === 'text' ? '0.6' : '1';
        }

        function checkPasswordStrength(value) {
            const container = document.getElementById('strength-container');
            const label     = document.getElementById('strength-label');
            const bars      = [
                document.getElementById('bar-1'),
                document.getElementById('bar-2'),
                document.getElementById('bar-3'),
                document.getElementById('bar-4'),
            ];

            if (!value) { container.style.display = 'none'; return; }
            container.style.display = 'block';

            let score = 0;
            if (value.length >= 8)  score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;

            const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'];
            const labels = ['Muito fraca', 'Fraca', 'Moderada', 'Forte'];
            const textColors = ['text-red-600', 'text-orange-500', 'text-yellow-600', 'text-green-600'];

            bars.forEach((bar, i) => {
                bar.className = 'h-1.5 flex-1 rounded-full transition-colors ' +
                    (i < score ? colors[score - 1] : 'bg-gray-200');
            });
            label.className = 'text-xs ' + (score > 0 ? textColors[score - 1] : 'text-gray-500');
            label.textContent = score > 0 ? labels[score - 1] : '';
        }

        document.getElementById('reset-password-form').addEventListener('submit', function () {
            const btn = document.getElementById('reset-submit-btn');
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Salvando...
            `;
        });
    </script>
</x-guest-layout>
