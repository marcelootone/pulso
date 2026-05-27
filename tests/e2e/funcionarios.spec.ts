import { test, expect } from '@playwright/test';

test.describe('CRUD de Funcionários', () => {
    // Fazer login como Gestor ou Secretaria antes de cada teste
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        
        await page.fill('input[name="email"]', 'admin@example.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL('/dashboard');
    });

    test('deve listar funcionários, criar, editar e desativar', async ({ page }) => {
        // Acessar listagem de funcionários
        await page.goto('/users');
        await expect(page.locator('h2')).toContainText('Gestão de Funcionários');

        // Testa o botão Novo
        await page.click('id=btn-novo-funcionario');
        await expect(page).toHaveURL('/users/create');

        // O formulário de criação original tem uma interface complexa.
        // Simulamos os campos de professor:
        await page.selectOption('select[name="tipo_usuario"]', 'Professor');
        await page.fill('input[name="cpf"]', '123.456.789-00');
        await page.fill('input[name="nome"]', 'Professor E2E Test');
        await page.fill('input[name="email"]', 'profe2e@example.com');
        await page.fill('input[name="password"]', 'password');
        
        await page.click('button:has-text("CADASTRAR")');

        await expect(page).toHaveURL('/users');
        await expect(page.locator('id=alert-success')).toBeVisible();

        // Agora encontra o funcionário recém criado para editar
        // Vamos usar um seletor que encontre o link "Editar" na linha que contém "Professor E2E Test"
        const row = page.locator('tr', { hasText: 'Professor E2E Test' }).first();
        await row.locator('text=✏️ Editar').click();

        await expect(page.locator('h2')).toContainText('Editar Funcionário');

        // Altera o telefone
        await page.fill('input[name="telefone"]', '999999999');
        await page.click('button:has-text("SALVAR ALTERAÇÕES")');

        // Deve redirecionar para a lista com sucesso
        await expect(page).toHaveURL('/users');
        await expect(page.locator('id=alert-success')).toBeVisible();

        // Entrar nos detalhes do usuário
        const updatedRow = page.locator('tr', { hasText: 'Professor E2E Test' }).first();
        await updatedRow.locator('text=Ver').click();

        await expect(page.locator('h2')).toContainText('Detalhes do Funcionário');
        
        // Voltar
        await page.click('text=⬅ Voltar');

        // Clica em desativar
        page.once('dialog', dialog => dialog.accept());
        
        const finalRow = page.locator('tr', { hasText: 'Professor E2E Test' }).first();
        await finalRow.locator('button:has-text("🚫 Desativar")').click();
        
        // Verifica mensagem de sucesso
        await expect(page.locator('id=alert-success')).toBeVisible();
    });
});
