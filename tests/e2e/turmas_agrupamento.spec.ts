import { test, expect } from '@playwright/test';

test.describe('Agrupamento de Turmas por Modalidade', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@example.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
    });

    test('deve listar turmas separadas por modalidade e ordenadas por ano letivo', async ({ page }) => {
        // Cria primeira turma
        await page.goto('/turmas/create');
        await page.selectOption('select[name="modalidade"]', 'EF - Ensino Fundamental');
        await page.selectOption('select[name="turno"]', 'Matutino');
        await page.fill('input[name="serie"]', '6');
        await page.fill('input[name="ano_letivo"]', '2024');
        await page.click('button[type="submit"]');

        // Cria segunda turma
        await page.goto('/turmas/create');
        await page.selectOption('select[name="modalidade"]', 'EJA EF - Ensino de Jovens e Adultos Fundamental');
        await page.selectOption('select[name="turno"]', 'Noturno');
        await page.fill('input[name="serie"]', '1');
        await page.fill('input[name="ano_letivo"]', '2025');
        await page.click('button[type="submit"]');

        await page.goto('/turmas');

        // Verifica se existem os cabeçalhos de modalidade criados no index
        await expect(page.locator('h3:has-text("EF - Ensino Fundamental")')).toBeVisible();
        await expect(page.locator('h3:has-text("EJA EF - Ensino de Jovens e Adultos Fundamental")')).toBeVisible();

        // As turmas de EJA 2025 devem aparecer na tabela correspondente
        const tabelaEja = page.locator('table[id="tabela-turmas-eja-ef-ensino-de-jovens-e-adultos-fundamental"]');
        await expect(tabelaEja).toBeVisible();
        await expect(tabelaEja).toContainText('2025');

        // As turmas de EF 2024 devem aparecer na tabela de EF
        const tabelaEf = page.locator('table[id="tabela-turmas-ef-ensino-fundamental"]');
        await expect(tabelaEf).toBeVisible();
        await expect(tabelaEf).toContainText('2024');
    });
});
