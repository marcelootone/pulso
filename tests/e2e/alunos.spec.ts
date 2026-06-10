import { test, expect } from '@playwright/test';

const EMAIL = 'gestor@sigae.edu.br';
const PASSWORD = 'password';

test.describe('Módulo Acadêmico - Alunos', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', EMAIL);
        await page.fill('input[name="password"]', PASSWORD);
        await page.click('button[type="submit"]');
        // wait for redirect to dashboard
        await page.waitForURL('**/dashboard*');
    });

    test('Deve renderizar a listagem de Alunos', async ({ page }) => {
        await page.goto('/alunos');
        await expect(page.locator('h2').first()).toContainText('Alunos');
    });

    test('Deve renderizar o formulário de cadastro de Alunos', async ({ page }) => {
        await page.goto('/alunos/create');
        await expect(page.locator('h2').first()).toContainText('Cadastrar');
        await expect(page.locator('form').first()).toBeVisible();
    });
});
