import { test, expect } from '@playwright/test';

const EMAIL = 'admin@email.com'; 
const PASSWORD = 'senha123';

test.describe('Módulo Central de Cadastros - Administrador', () => {
    
    test.beforeEach(async ({ page }) => {
        // Login antes de cada teste
        await page.goto('/login');
        await page.fill('input[name="email"]', EMAIL);
        await page.fill('input[name="password"]', PASSWORD);
        await page.click('button[type="submit"]');
        
        // Verifica se logou corretamente
        await expect(page).toHaveURL('/dashboard');
    });

    test('Administrador deve conseguir acessar o formulário e criar um Gestor', async ({ page }) => {
        await page.goto('/users/create');
        await expect(page.locator('h2')).toContainText('Cadastro Manual de Usuário');

        // Selecionar o tipo de usuário como GESTOR
        await page.selectOption('select[name="tipo_usuario"]', 'Gestor');

        // Preencher dados básicos
        const uniqueSuffix = Date.now().toString().slice(-6);
        await page.fill('input[name="nome"]', `Gestor Teste ${uniqueSuffix}`);
        await page.fill('input[name="email"]', `gestorteste${uniqueSuffix}@email.com`);
        await page.fill('input[name="password"]', 'senha123');

        // Submeter formulário
        await page.click('button[type="submit"]:has-text("Cadastrar Usuário")');

        // Verificar mensagem de sucesso
        await expect(page.locator('.text-green-800, .bg-green-100')).toContainText('Usuário cadastrado com sucesso!');
    });
});
