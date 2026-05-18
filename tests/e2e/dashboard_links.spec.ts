import { test, expect } from '@playwright/test';

test.describe('Dashboard Links Navigation', () => {
  test.beforeEach(async ({ page }) => {
    // Fazer login primeiro como admin (perfil restrito)
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@sigae.edu.br');
    await page.fill('input[name="password"]', 'password'); // Assume a senha padrão
    await page.click('button[type="submit"]');
    
    // Aguardar redirecionamento para o dashboard
    await page.waitForURL('/dashboard');
  });

  test('Deve exibir todos os botões de atalho no dashboard', async ({ page }) => {
    // Validar presença de todos os links principais
    await expect(page.locator('a:has-text("Criar Usuário")')).toBeVisible();
    await expect(page.locator('a:has-text("Importar Estudantes")')).toBeVisible();
    await expect(page.locator('a:has-text("Turmas")')).toBeVisible();
    await expect(page.locator('a:has-text("Eletivas")')).toBeVisible();
    await expect(page.locator('a:has-text("Atribuir Aulas")')).toBeVisible();
    await expect(page.locator('a:has-text("Meu Diário")')).toBeVisible();
    await expect(page.locator('a:has-text("Agendamentos")')).toBeVisible();
    await expect(page.locator('a:has-text("Espaços")')).toBeVisible();
  });
});
