import { test, expect } from '@playwright/test';

test.describe('Cadastro Manual de Usuários', () => {

    test.beforeEach(async ({ page }) => {
        // Assume que existe um login admin padrão
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('/dashboard');
    });

    test('deve alternar os blocos de Aluno e Funcionário corretamente', async ({ page }) => {
        await page.goto('/users/create');

        // Blocos 3 e 4 devem estar escondidos inicialmente
        await expect(page.locator('text=Bloco 3: Dados Acadêmicos')).toBeHidden();
        await expect(page.locator('text=Bloco 4: Credenciais de Acesso')).toBeHidden();

        // Selecionar "Aluno"
        await page.selectOption('select[name="tipo_usuario"]', 'Aluno');
        
        // Bloco 3 deve aparecer, Bloco 4 não
        await expect(page.locator('text=Bloco 3: Dados Acadêmicos')).toBeVisible();
        await expect(page.locator('text=Bloco 4: Credenciais de Acesso')).toBeHidden();
        
        // Campo RA deve estar visível e ser obrigatório
        const raInput = page.locator('input[name="ra"]');
        await expect(raInput).toBeVisible();
        await expect(raInput).toHaveAttribute('required', '');

        // Selecionar "Professor"
        await page.selectOption('select[name="tipo_usuario"]', 'Professor');
        
        // Bloco 4 deve aparecer, Bloco 3 não
        await expect(page.locator('text=Bloco 3: Dados Acadêmicos')).toBeHidden();
        await expect(page.locator('text=Bloco 4: Credenciais de Acesso')).toBeVisible();

        // Campos de login devem estar visíveis e obrigatórios
        const usernameInput = page.locator('input[name="username"]');
        const passwordInput = page.locator('input[name="password"]');
        await expect(usernameInput).toBeVisible();
        await expect(usernameInput).toHaveAttribute('required', '');
        await expect(passwordInput).toBeVisible();
        await expect(passwordInput).toHaveAttribute('required', '');
    });

    test('deve cadastrar um novo Aluno com sucesso', async ({ page }) => {
        await page.goto('/users/create');

        await page.selectOption('select[name="tipo_usuario"]', 'Aluno');
        
        await page.fill('input[name="ra"]', 'RA' + Date.now());
        await page.fill('input[name="cpf"]', '123456789' + Math.floor(Math.random() * 100));
        await page.fill('input[name="nome"]', 'João Teste Aluno');
        await page.fill('input[name="nascimento"]', '2010-05-10');
        await page.selectOption('select[name="sexo"]', 'M');
        await page.fill('input[name="telefone"]', '11999999999');
        await page.fill('input[name="email"]', 'aluno' + Date.now() + '@teste.com');
        await page.fill('input[name="nome_mae"]', 'Maria Teste');

        await page.click('button:has-text("Salvar Cadastro")');

        await expect(page.locator('text=Usuário cadastrado com sucesso!')).toBeVisible();
    });

    test('deve cadastrar um novo Professor com sucesso', async ({ page }) => {
        await page.goto('/users/create');

        await page.selectOption('select[name="tipo_usuario"]', 'Professor');
        
        await page.fill('input[name="nome"]', 'Carlos Teste Professor');
        await page.fill('input[name="username"]', 'prof.carlos' + Date.now());
        await page.fill('input[name="password"]', 'senhaforte123');

        await page.click('button:has-text("Salvar Cadastro")');

        await expect(page.locator('text=Usuário cadastrado com sucesso!')).toBeVisible();
    });
});
