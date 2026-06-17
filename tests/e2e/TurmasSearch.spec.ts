import { test, expect } from '@playwright/test';

test.describe('Pesquisa de Turmas', () => {
    test('Deve permitir a busca por turmas usando termo de pesquisa', async ({ page }) => {
        // 1. Fazer login
        await page.goto('/login');
        const emailInput = page.locator('input[name="email"], input[type="email"], input[name="cpf"]');
        if (await emailInput.isVisible()) {
            await emailInput.fill('gestor@sigae.test'); // Assume admin ou gestor
            await page.locator('input[type="password"]').fill('password');
            await page.locator('button[type="submit"]').click();
        }

        // 2. Navegar para a página de Turmas
        await page.goto('/turmas');
        await expect(page.locator('text=Gestão de Turmas')).toBeVisible();

        // 3. Preencher o campo de busca
        const searchInput = page.locator('input[name="search"]');
        await expect(searchInput).toBeVisible();

        // Pesquisar por uma turma específica ou nome que sabemos que existe ou não existe
        await searchInput.fill('XYZ_BuscaInexistente_123');
        await searchInput.press('Enter');

        // 4. Verificar se a página recarregou e exibe a mensagem de que não há turmas
        await expect(page.locator('text=Nenhuma turma encontrada')).toBeVisible();

        // Limpar pesquisa
        await page.locator('a[href*="search="] .w-5.h-5').click(); // x-mark icon
        await expect(searchInput).toHaveValue('');
        
        // Fazer uma busca mais genérica
        await searchInput.fill('EF'); // Ensino Fundamental ou tipo EF
        await searchInput.press('Enter');
        
        // Verifica se carregou
        await expect(page.locator('text=Gestão de Turmas')).toBeVisible();

        // Fazer busca por docente
        await searchInput.fill('João'); // Professor fictício
        await searchInput.press('Enter');
        
        await expect(page.locator('text=Gestão de Turmas')).toBeVisible();
    });
});
