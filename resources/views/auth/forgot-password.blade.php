<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Esqueceu sua senha?</h2>
        <p class="text-sm text-gray-500 mt-1">
            Informe seu e-mail e enviaremos um link para você criar uma nova senha.
        </p>
    </div>

    {{-- Status de sucesso --}}
    @if (session('status'))
        <div class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6" id="forgot-password-form" novalidate>
        @csrf

        {{-- E-mail --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail cadastrado</label>
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
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="seu@email.com"
                    class="block w-full pl-10 pr-3 py-2.5 border {{ $errors->has('email') ? 'border-red-400 focus:ring-red-500 focus:border-red-400' : 'border-gray-300 focus:ring-primary-500 focus:border-primary-500' }} rounded-lg sm:text-sm transition-colors"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Botão --}}
        <div>
            <button
                id="submit-btn"
                type="submit"
                class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Enviar link de recuperação
            </button>
        </div>

        {{-- Voltar para login --}}
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500 transition-colors inline-flex items-center gap-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar para o login
            </a>
        </div>
    </form>

    <script>
        document.getElementById('forgot-password-form').addEventListener('submit', function () {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Enviando...
            `;
        });
    </script>
</x-guest-layout>
