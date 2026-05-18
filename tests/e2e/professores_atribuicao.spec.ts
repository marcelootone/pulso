import { test, expect } from '@playwright/test';

test.describe('Professor Creation and Attribution Flow', () => {

    test.beforeEach(async ({ page }) => {
        // Assume que existe um login admin padrão
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@sigae.edu.br'); // Atualizado para o e-mail comum no sistema
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('/dashboard');
    });

    test('deve criar um professor e exibi-lo na tela de atribuir aulas', async ({ page }) => {
        const uniqueProfName = `Prof Teste ${Date.now()}`;
        const uniqueEmail = `prof${Date.now()}@teste.com`;

        // 1. Criar Professor
        await page.goto('/users/create');

        // Seleciona a constante TIPO_PROFESSOR ("Professor")
        await page.selectOption('select[name="tipo_usuario"]', 'Professor');
        
        await page.fill('input[name="nome"]', uniqueProfName);
        await page.fill('input[name="email"]', uniqueEmail);
        await page.fill('input[name="password"]', 'senhaforte123');

        await page.click('button:has-text("CADASTRAR")');

        await expect(page.locator('text=Usuário cadastrado com sucesso!')).toBeVisible();

        // 2. Verificar se aparece na tela de atribuição de aulas
        await page.goto('/atribuir-aulas');
        
        // Clica ou foca no select de professores
        const profSelect = page.locator('select[name="user_id"]');
        await expect(profSelect).toBeVisible();
        
        // Verifica se a opção com o nome do professor existe no select
        const option = profSelect.locator(`option:has-text("${uniqueProfName}")`);
        await expect(option).toBeAttached();
    });
});
