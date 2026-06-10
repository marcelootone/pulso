import { test, expect } from '@playwright/test';

const EMAIL = 'gestor@sigae.edu.br';
const PASSWORD = 'password';

test.describe('Módulo Pedagógico - Meu Diário', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', EMAIL);
        await page.fill('input[name="password"]', PASSWORD);
        await page.click('button[type="submit"]');
        // wait for redirect
        await page.waitForURL('**/dashboard*');
    });

    test('Deve acessar a visão geral do Diário', async ({ page }) => {
        await page.goto('/diario');
        // Verify we are on the daily page or similar
        await expect(page.locator('h2').first()).toContainText('Diário');
    });
});
