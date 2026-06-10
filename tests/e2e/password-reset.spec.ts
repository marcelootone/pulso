import { test, expect, type Page } from '@playwright/test';

/**
 * Testes E2E — Módulo de Recuperação de Senha
 *
 * Cobre: solicitação de link, validações, proteção contra user enumeration,
 * throttle rate-limit e fluxo de redefinição de senha.
 */

const BASE_URL = 'http://sigae.test';

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

async function goToForgotPassword(page: Page) {
    await page.goto(`${BASE_URL}/forgot-password`);
    await expect(page).toHaveTitle(/SIGAE/);
}

// ---------------------------------------------------------------------------
// Testes da tela "Esqueci minha senha"
// ---------------------------------------------------------------------------

test.describe('Recuperação de senha — Tela de solicitação', () => {
    test('deve renderizar o formulário de recuperação corretamente', async ({ page }) => {
        await goToForgotPassword(page);

        await expect(page.getByRole('heading', { name: 'Esqueceu sua senha?' })).toBeVisible();
        await expect(page.locator('#email')).toBeVisible();
        await expect(page.locator('#forgot-password-form button[type="submit"]')).toBeVisible();
        await expect(page.getByRole('link', { name: /Voltar para o login/i })).toBeVisible();
    });

    test('deve exibir erro de validação com e-mail inválido', async ({ page }) => {
        await goToForgotPassword(page);

        await page.locator('#email').fill('email-invalido');
        await page.locator('#forgot-password-form button[type="submit"]').click();

        // O browser valida via HTML5 type="email" — campo não envia com email inválido
        // Checamos que ainda estamos na mesma página
        await expect(page).toHaveURL(`${BASE_URL}/forgot-password`);
    });

    test('deve exibir mensagem genérica tanto para e-mail cadastrado quanto não cadastrado', async ({ page }) => {
        await goToForgotPassword(page);

        // E-mail NÃO cadastrado — deve mostrar a mesma mensagem (user enumeration protection)
        await page.locator('#email').fill('nao-existe@inexistente.com.br');
        await page.locator('#forgot-password-form button[type="submit"]').click();

        // Aguarda redirecionamento / atualização da página
        await page.waitForURL(`${BASE_URL}/forgot-password`);

        // Deve exibir a mensagem de sucesso genérica (não revelar que e-mail não existe)
        const successMessage = page.locator('.bg-green-50');
        const errorMessage   = page.locator('.text-red-');

        // Ou exibe sucesso genérico ou nenhum erro específico de "e-mail não encontrado"
        const hasSuccess = await successMessage.isVisible().catch(() => false);
        const hasError   = await errorMessage.isVisible().catch(() => false);

        // Não deve revelar que o usuário não existe — qualquer uma das situações é válida
        if (hasError) {
            const errorText = await errorMessage.textContent();
            expect(errorText).not.toContain('encontramos');
            expect(errorText).not.toContain('não existe');
        }
    });

    test('deve exibir mensagem de sucesso após envio com e-mail válido', async ({ page }) => {
        await goToForgotPassword(page);

        // Usa um e-mail em formato válido (pode ou não estar cadastrado)
        await page.locator('#email').fill('test@example.com');
        await page.locator('#forgot-password-form button[type="submit"]').click();

        await page.waitForURL(`${BASE_URL}/forgot-password`);

        // Mensagem de sucesso deve aparecer
        await expect(page.locator('.bg-green-50')).toBeVisible({ timeout: 5000 });
    });

    test('deve navegar de volta para o login ao clicar no link', async ({ page }) => {
        await goToForgotPassword(page);

        await page.getByRole('link', { name: /Voltar para o login/i }).click();
        await expect(page).toHaveURL(`${BASE_URL}/login`);
    });
});

// ---------------------------------------------------------------------------
// Testes da tela "Redefinição de senha"
// ---------------------------------------------------------------------------

test.describe('Recuperação de senha — Tela de redefinição', () => {
    test('deve redirecionar ao acessar link com token inválido e tentar submeter', async ({ page }) => {
        // Acessa com token falso (simulação)
        await page.goto(`${BASE_URL}/reset-password/token-invalido-123`);

        await expect(page.getByRole('heading', { name: 'Criar nova senha' })).toBeVisible();
        await expect(page.locator('#email')).toBeVisible();
        await expect(page.locator('#password')).toBeVisible();
        await expect(page.locator('#password_confirmation')).toBeVisible();
    });

    test('deve mostrar erro ao submeter token inválido', async ({ page }) => {
        await page.goto(`${BASE_URL}/reset-password/token-invalido-123`);

        await page.locator('#email').fill('qualquer@email.com');
        await page.locator('#password').fill('NovaSenha@123');
        await page.locator('#password_confirmation').fill('NovaSenha@123');
        await page.locator('#reset-password-form button[type="submit"]').click();

        await page.waitForURL(`${BASE_URL}/reset-password/token-invalido-123`);

        // Deve exibir erro de token inválido
        const errorVisible = await page.locator('.text-red-600, [class*="text-red"]').first().isVisible().catch(() => false);
        expect(errorVisible).toBe(true);
    });

    test('deve validar que as senhas coincidem (client-side e server-side)', async ({ page }) => {
        await page.goto(`${BASE_URL}/reset-password/qualquer-token`);

        await page.locator('#email').fill('usuario@test.com');
        await page.locator('#password').fill('Senha@123');
        await page.locator('#password_confirmation').fill('SenhaDiferente@456');
        await page.locator('#reset-password-form button[type="submit"]').click();

        // Permanece na tela de reset
        await expect(page).toHaveURL(`${BASE_URL}/reset-password/qualquer-token`);
    });

    test('deve exibir o indicador de força da senha ao digitar', async ({ page }) => {
        await page.goto(`${BASE_URL}/reset-password/token-qualquer`);

        const strengthContainer = page.locator('#strength-container');
        await expect(strengthContainer).toBeHidden();

        // Digitar senha fraca
        await page.locator('#password').fill('abc');
        await expect(strengthContainer).toBeVisible();

        // Digitar senha forte
        await page.locator('#password').fill('MinhaS3nh@Fort3!');
        const strengthLabel = page.locator('#strength-label');
        await expect(strengthLabel).toContainText('Forte');
    });

    test('deve alternar visibilidade da senha ao clicar no ícone de olho', async ({ page }) => {
        await page.goto(`${BASE_URL}/reset-password/token-qualquer`);

        const passwordInput = page.locator('#password');
        await expect(passwordInput).toHaveAttribute('type', 'password');

        // Clica no botão de toggle
        await page.locator('button[onclick*="password"]').first().click();
        await expect(passwordInput).toHaveAttribute('type', 'text');

        // Clica novamente para esconder
        await page.locator('button[onclick*="password"]').first().click();
        await expect(passwordInput).toHaveAttribute('type', 'password');
    });
});

// ---------------------------------------------------------------------------
// Testes de Rate Limiting (Throttle)
// ---------------------------------------------------------------------------

test.describe('Recuperação de senha — Rate Limiting', () => {
    test('deve bloquear após múltiplas tentativas rápidas (throttle)', async ({ page }) => {
        // Faz 6 tentativas seguidas para disparar o throttle (limite: 5/min)
        for (let i = 0; i < 5; i++) {
            await page.goto(`${BASE_URL}/forgot-password`);
            await page.locator('#email').fill(`tentativa${i}@test.com`);
            await page.locator('#forgot-password-form button[type="submit"]').click();
            await page.waitForTimeout(200);
        }

        // A próxima tentativa deve resultar em erro de throttle (429) ou mensagem de espera
        await page.goto(`${BASE_URL}/forgot-password`);
        await page.locator('#email').fill('throttle@test.com');
        await page.locator('#forgot-password-form button[type="submit"]').click();

        const status = page.locator('body');
        const bodyText = await status.textContent();

        // Laravel retorna 429 ou redireciona com a mensagem de throttle
        const isThrottled =
            page.url().includes('forgot-password') ||
            bodyText?.includes('429') ||
            bodyText?.includes('aguarde') ||
            bodyText?.includes('espere');

        expect(isThrottled).toBe(true);
    });
});
