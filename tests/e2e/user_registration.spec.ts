import { test, expect } from '@playwright/test';

test.describe('Cadastro Manual de Usuários', () => {

    test.beforeEach(async ({ page }) => {
        // Assume que existe um login admin padrão
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@example.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('/dashboard');
    });

    test('não oferece o perfil de estudante no cadastro de usuários', async ({ page }) => {
        await page.goto('/users/create');

        await expect(page.locator('select[name="tipo_usuario"] option[value="Estudante"]')).toHaveCount(0);
        await expect(page.locator('input[name="ra"]')).toHaveCount(0);
        await expect(page.locator('input[name="email"]')).toHaveAttribute('required', '');
        await expect(page.locator('input[name="password"]')).toHaveAttribute('required', '');
    });

    test('deve cadastrar um novo Professor com sucesso', async ({ page }) => {
        await page.goto('/users/create');

        await page.selectOption('select[name="tipo_usuario"]', 'Professor');
        
        await page.fill('input[name="nome"]', 'Carlos Teste Professor');
        await page.fill('input[name="email"]', 'prof.carlos' + Date.now() + '@teste.com');
        await page.fill('input[name="password"]', 'senhaforte123');

        await page.click('button:has-text("Cadastrar Usuário")');

        await expect(page.locator('text=Usuário cadastrado com sucesso!')).toBeVisible();
    });
});
