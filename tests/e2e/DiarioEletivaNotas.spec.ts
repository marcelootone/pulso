import { test, expect } from '@playwright/test';

test.describe('Diário de Eletivas - Notas', () => {
    test('Deve ser possível criar uma prova sem lançar notas e ver no histórico', async ({ page }) => {
        // Assume user is already logged in or needs to log in
        // In this test environment, we assume the test setup handles authentication, or we do it here.
        await page.goto('/login');
        
        // Fill login if necessary (adjust selectors according to the app)
        const emailInput = page.locator('input[name="email"], input[type="email"], input[name="cpf"]');
        if (await emailInput.isVisible()) {
            await emailInput.fill('gestor@sigae.test'); // example login, adjust if necessary
            await page.locator('input[type="password"]').fill('password');
            await page.locator('button[type="submit"]').click();
        }

        // Navigate to the eletiva diario, assuming eletiva ID 3 exists and usa_nota = 1
        await page.goto('/eletivas/3/diario?tab=notas');

        // Check if we are on the notas tab
        await expect(page.locator('h3:has-text("Lançamento de Notas")')).toBeVisible();

        // Fill data_avaliacao and descricao
        const dataAvaliacao = '2023-11-20';
        const descricao = 'Prova de Playwright';

        await page.locator('input[name="data_avaliacao"]').fill(dataAvaliacao);
        await page.locator('input[name="descricao"]').fill(descricao);

        // Leave notes blank to test creation of empty assessment
        
        // Click save
        await page.locator('button:has-text("Salvar Notas")').click();

        // Should redirect to action=ver and show success message
        await expect(page.locator('text=Notas salvas com sucesso!')).toBeVisible();
        await expect(page.locator(`input[name="descricao"]`)).toHaveValue(descricao);

        // Click Edit
        await page.locator('a:has-text("Editar")').first().click();

        // Ensure we are in edit mode
        await expect(page.locator('button:has-text("Salvar Notas")')).toBeVisible();
        await expect(page.locator(`input[name="descricao"]`)).toHaveValue(descricao);
    });
});
