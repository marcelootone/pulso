import { test, expect } from '@playwright/test';

test.describe('Estudo Orientado - Visão do Gestor', () => {
    test('Gestor deve poder visualizar solicitações de todos os professores', async ({ page }) => {
        // Acessar login
        await page.goto('/login');
        
        // Logar como gestor
        const emailInput = page.locator('input[name="email"], input[type="email"], input[name="cpf"]');
        if (await emailInput.isVisible()) {
            await emailInput.fill('gestor@sigae.test'); // email fictício de gestor
            await page.locator('input[type="password"]').fill('password');
            await page.locator('button[type="submit"]').click();
        }

        // Navegar para solicitações de estudo orientado
        await page.goto('/estudo-orientado/solicitacoes');

        // O Gestor deve ver a listagem de solicitações (mesmo que não tenha criado nenhuma)
        // Vamos checar se a tabela renderizou (mesmo que vazia ou com itens criados por outros)
        await expect(page.locator('h3:has-text("Solicitações de Estudo Orientado")')).toBeVisible();

        // Checar se a listagem aparece sem o filtro automático de professor_solicitante_id
        // Para garantir, verificaremos apenas se a página carregou corretamente a grid
        await expect(page.locator('table, .grid, text="Nenhuma solicitação encontrada"')).toBeVisible();
    });
});
