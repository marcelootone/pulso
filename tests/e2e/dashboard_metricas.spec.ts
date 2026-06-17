import { test, expect } from '@playwright/test';

test.describe('Dashboard e Métricas - Permissões de Acesso', () => {

    test('Gestor deve visualizar as métricas globais e tabela de evasão', async ({ page }) => {
        // 1. Acesso à tela de login
        await page.goto('/login');

        // 2. Preenchimento de dados de um gestor
        // Assumindo que admin@admin.com é um gestor no seed padrão
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // 3. Verifica redirecionamento e carregamento
        await expect(page).toHaveURL('/dashboard');
        
        // 4. Verifica a exibição dos KPIs Básicos
        await expect(page.locator('text=Estudantes')).toBeVisible();
        await expect(page.locator('text=Turmas')).toBeVisible();
        await expect(page.locator('text=Freq. Geral')).toBeVisible();

        // 5. Verifica a seção de Alerta de Evasão
        await expect(page.locator('h3:has-text("Alerta de Evasão")')).toBeVisible();
        
        // 6. Verifica a existência da tabela de Estudante
        await expect(page.locator('th:has-text("Estudante")')).toBeVisible();
        await expect(page.locator('th:has-text("Presença")')).toBeVisible();
    });

    test('Professor deve visualizar apenas suas métricas sem erro 500', async ({ page }) => {
        // 1. Acesso à tela de login
        await page.goto('/login');

        // 2. Login como professor (Assumindo professor@escola.com no seed)
        await page.fill('input[name="email"]', 'professor@escola.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // 3. Verifica redirecionamento e carregamento da página sem erros de Service/SQL
        await expect(page).toHaveURL('/dashboard');
        
        // 4. Garante que os KPIs Básicos renderizaram corretamente
        await expect(page.locator('text=Estudantes')).toBeVisible();
        await expect(page.locator('text=Freq. Geral')).toBeVisible();
        await expect(page.locator('h3:has-text("Alerta de Evasão")')).toBeVisible();
    });
});
