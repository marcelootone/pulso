import { test, expect } from '@playwright/test';

test.describe('Monitoramento de Frequência - Abas por Disciplina', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        
        // Login as Gestor/Admin
        await page.fill('input[name="email"]', 'admin@example.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL('/dashboard');
    });

    test('deve visualizar as abas de disciplinas e confirmar alteração', async ({ page }) => {
        // Acessa a página de Monitoramento
        await page.goto('/frequencia/monitorar');
        await expect(page.locator('h2')).toContainText('Visualizar Chamada');

        const turmaSelect = page.locator('select[name="turma_id"]');
        const options = await turmaSelect.locator('option').allInnerTexts();
        
        if (options.length > 1) {
            // Seleciona a primeira turma disponível
            await turmaSelect.selectOption({ index: 1 });
            
            // Aguarda o carregamento das abas ou mensagem de nenhuma disciplina
            await page.waitForTimeout(1000); // Dar tempo para o carregamento do DOM
            
            const nenhumaDisciplina = await page.locator('text=Nenhuma disciplina registrada').isVisible();
            
            if (!nenhumaDisciplina) {
                // Se houver disciplinas, deve ter botões de abas
                const abas = page.locator('button.border-b-2');
                const abasCount = await abas.count();
                expect(abasCount).toBeGreaterThan(0);
                
                // Clica na primeira aba
                await abas.first().click();
                
                // Altera o status de um aluno na aba atual (forçar seleção)
                const radioFalta = page.locator('input[type="radio"][value="F"]').first();
                if (await radioFalta.isVisible()) {
                    await radioFalta.click({ force: true });
                    
                    // Clica no botão Salvar Alteração para abrir o Modal
                    await page.click('button:has-text("Salvar Alteração")');
                    
                    // Verifica se o modal abriu
                    const modalTitle = page.locator('text=Confirmar alteração de frequência');
                    await expect(modalTitle).toBeVisible();
                    
                    // Clica em Confirmar Alteração dentro do modal
                    await page.click('button:has-text("Confirmar Alteração")');
                    
                    // Verifica mensagem de sucesso
                    await expect(page.locator('text=Frequência salva com sucesso!')).toBeVisible();
                }
            }
        }
    });
});
