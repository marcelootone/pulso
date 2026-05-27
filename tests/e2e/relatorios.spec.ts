import { test, expect } from '@playwright/test';

test.describe('Módulo de Relatórios (PDF)', () => {
    test.beforeEach(async ({ page }) => {
        // Fazer login como Gestor/Secretaria
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@example.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL('/dashboard');
    });

    test('deve acessar central de relatórios e verificar presença dos relatórios', async ({ page }) => {
        // Acessa central de relatórios
        await page.goto('/relatorios');
        await expect(page.locator('h2')).toContainText('Central de Relatórios');

        // Verifica a presença dos 3 relatórios
        await expect(page.locator('h3:has-text("Alerta de Evasão")')).toBeVisible();
        await expect(page.locator('h3:has-text("Frequência da Turma")')).toBeVisible();
        await expect(page.locator('h3:has-text("Ranking de Faltas")')).toBeVisible();

        // No playwright, testar o download real de PDF muitas vezes exige setup específico (interceptar buffer).
        // Vamos apenas garantir que os botões/formulários estão configurados corretamente com a action.
        
        // Verifica o botão de download de evasão (link direto)
        const evasaoLink = page.locator('a:has-text("BAIXAR PDF")');
        await expect(evasaoLink).toHaveAttribute('href', /.*\/relatorio-evasao/);

        // Verifica os formulários GET para os relatórios com filtro
        const formFrequencia = page.locator('form[action$="/relatorios/frequencia-mensal"]');
        await expect(formFrequencia).toBeVisible();

        const formRanking = page.locator('form[action$="/relatorios/turmas-faltas"]');
        await expect(formRanking).toBeVisible();
    });
});
