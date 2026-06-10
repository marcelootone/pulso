import { test, expect } from '@playwright/test';

// Utilizamos um e-mail de teste ou criamos um comando Artisan para criar um usuário de teste
// Se o seed foi rodado, existe 'admin@example.com' com senha 'password' ou similar
// Aqui é um teste básico de renderização (smoke test) para os módulos recém refatorados
const EMAIL = 'admin@admin.com'; 
const PASSWORD = 'password';

test.describe('Módulos Adicionais - Fluxo Básico de Visualização', () => {
    
    test.beforeEach(async ({ page }) => {
        // Login antes de cada teste
        await page.goto('/login');
        // Preenche credenciais genéricas que se espera existir na base (ou adpte se houver outro usuário padrão no SIGAE)
        await page.fill('input[name="email"]', EMAIL);
        await page.fill('input[name="password"]', PASSWORD);
        await page.click('button[type="submit"]');
        
        // Verifica se logou corretamente
        await expect(page).toHaveURL('/dashboard');
    });

    test('Deve renderizar a tela de Eletivas e ter o botão Criar Eletiva', async ({ page }) => {
        await page.goto('/eletivas');
        await expect(page.locator('h2')).toContainText('Gestão de Eletivas');
        await expect(page.locator('text=Criar Eletiva')).toBeVisible();
    });

    test('Deve renderizar a tela de Estudo Orientado (Solicitações)', async ({ page }) => {
        await page.goto('/estudo-orientado/solicitacoes');
        await expect(page.locator('h2')).toContainText('Estudo Orientado');
        await expect(page.locator('text=Solicitar Atividade')).toBeVisible();
    });

    test('Deve renderizar a tela de Estudo Orientado (Avaliações)', async ({ page }) => {
        await page.goto('/estudo-orientado/avaliacoes');
        await expect(page.locator('text=Atividades para Avaliar')).toBeVisible();
    });

    test('Deve renderizar a tela de Planejamento Semanal', async ({ page }) => {
        await page.goto('/planejamento-semanal');
        await expect(page.locator('h2')).toContainText('Planejamento Semanal');
        await expect(page.locator('text=Adicionar Horário')).toBeVisible();
    });

    test('Deve renderizar a tela de Gestão de Espaços', async ({ page }) => {
        await page.goto('/espacos');
        await expect(page.locator('h2')).toContainText('Gestão de Espaços');
        await expect(page.locator('text=Criar Espaço')).toBeVisible();
    });

    test('Deve renderizar a Central de Relatórios', async ({ page }) => {
        await page.goto('/relatorios');
        await expect(page.locator('h2')).toContainText('Central de Relatórios');
        await expect(page.locator('text=Alerta de Evasão')).toBeVisible();
        await expect(page.locator('text=Frequência da Turma')).toBeVisible();
        await expect(page.locator('text=Ranking de Faltas')).toBeVisible();
    });
});
