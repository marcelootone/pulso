import { test, expect } from '@playwright/test';

test.describe('Layout Principal e Navegação', () => {
  test('deve logar e carregar a nova estrutura de layout', async ({ page }) => {
    // Acessa o login
    await page.goto('/login');
    
    // Preenche credenciais
    await page.fill('input[name="email"]', 'admin@email.com');
    await page.fill('input[name="password"]', 'senha123');
    await page.click('button[type="submit"]');

    // Espera navegar para a página principal
    await page.waitForURL('**/dashboard');

    // Verifica elementos do novo layout
    // Sidebar Logo
    await expect(page.getByText('SIGAE', { exact: true })).toBeVisible();
    
    // Topbar 
    await expect(page.locator('header')).toBeVisible();

    // Menu Sidebar (exemplo Acadêmico e Pedagógico)
    await expect(page.getByText('Acadêmico', { exact: true })).toBeVisible();
    await expect(page.getByText('Pedagógico', { exact: true })).toBeVisible();
  });
});
