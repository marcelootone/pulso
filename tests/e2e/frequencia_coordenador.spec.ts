import { test, expect } from '@playwright/test';

test.describe('Monitoramento e Busca Ativa (Coordenador)', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        
        // Fazer login como Coordenador (ou gestor/admin)
        await page.fill('input[name="email"]', 'admin@example.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL('/dashboard');
    });

    test('deve visualizar o painel, lançar frequência e verificar busca ativa', async ({ page }) => {
        // Acessar Monitoramento
        await page.goto('/frequencia');
        await expect(page.locator('h2')).toContainText('Monitoramento Escolar');

        // Navegar para lançamento de chamada
        await page.click('text=Lançar Chamada');
        await expect(page).toHaveURL(/.*\/frequencia\/monitorar/);

        // O select de turmas só renderiza as ativas. Vamos forçar a seleção se houver alguma
        const turmaSelect = page.locator('select[name="turma_id"]');
        const options = await turmaSelect.locator('option').allInnerTexts();
        
        if (options.length > 1) {
            // Seleciona a primeira turma (índice 1 pois 0 é o placeholder)
            await turmaSelect.selectOption({ index: 1 });
            
            // Espera carregar a lista
            await page.waitForSelector('table tbody tr');
            
            // Marca a primeira pessoa com falta (F)
            const radioFalta = page.locator('input[type="radio"][value="F"]').first();
            await radioFalta.click({ force: true });
            
            // Salvar chamada
            await page.click('button:has-text("SALVAR CHAMADA")');
            await expect(page.locator('text=Sucesso!')).toBeVisible();
        }

        // Navegar para Busca Ativa
        await page.goto('/frequencia/busca-ativa');
        await expect(page.locator('h2')).toContainText('Busca Ativa');

        // Verifica se a tabela/lista carregou
        // Se houver algum aluno em risco, deve ser possível registrar ação
        const salvarRegistroBtn = page.locator('button:has-text("Salvar Registro")').first();
        if (await salvarRegistroBtn.isVisible()) {
            await page.fill('textarea[name="observacao"]', 'Contato E2E de teste');
            await salvarRegistroBtn.click();
            await expect(page.locator('text=Registro de busca ativa salvo com sucesso!')).toBeVisible();
        }
    });
});
