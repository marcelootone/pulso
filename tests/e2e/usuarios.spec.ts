import { test, expect } from '@playwright/test';

const EMAIL = 'admin@admin.com'; 
const PASSWORD = 'password';

test.describe('Módulo Central de Cadastros - Funcionários', () => {
    
    test.beforeEach(async ({ page }) => {
        // Login antes de cada teste
        await page.goto('/login');
        await page.fill('input[name="email"]', EMAIL);
        await page.fill('input[name="password"]', PASSWORD);
        await page.click('button[type="submit"]');
        
        // Verifica se logou corretamente
        await expect(page).toHaveURL('/dashboard');
    });

    test('Deve renderizar a tela de listagem de Funcionários', async ({ page }) => {
        await page.goto('/users');
        await expect(page.locator('h2')).toContainText('Gestão de Funcionários');
        await expect(page.locator('text=Novo Funcionário')).toBeVisible();
        await expect(page.locator('table')).toBeVisible();
    });

    test('Deve renderizar a tela de criação de Funcionários', async ({ page }) => {
        await page.goto('/users/create');
        await expect(page.locator('h2')).toContainText('Cadastro Manual de Usuário');
        await expect(page.locator('text=Cadastrar Usuário')).toBeVisible();
        await expect(page.locator('text=Tipo de Perfil e Documentos')).toBeVisible();
    });
});
