import { test, expect } from '@playwright/test';

test.describe('CRUD de Turmas', () => {
    // Fazer login como Secretaria antes de cada teste
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        
        // Aqui assumimos que já existe um usuário de teste semeado
        // ou usamos um mock de login. Para o teste e2e básico, 
        // usaremos um login válido se houver, ou precisaremos do Factory.
        // Vamos preencher o formulário:
        await page.fill('input[name="email"]', 'admin@example.com'); // Substituir pelo admin do seed
        await page.fill('input[name="password"]', 'password'); // Senha padrão
        await page.click('button[type="submit"]');

        // Esperar o painel carregar
        await expect(page).toHaveURL('/dashboard');
    });

    test('deve listar turmas, editar e desativar', async ({ page }) => {
        // Acessar listagem de turmas
        await page.goto('/turmas');
        await expect(page.locator('h2')).toContainText('Gestão de Turmas');

        // Cria uma turma nova para não interferir em outras
        await page.click('id=btn-nova-turma');
        await expect(page).toHaveURL('/turmas/create');

        await page.selectOption('select[name="modalidade"]', 'EF - Ensino Fundamental');
        await page.selectOption('select[name="turno"]', 'Matutino');
        await page.fill('input[name="serie"]', '9');
        await page.fill('input[name="complemento"]', 'E2E');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL('/turmas');
        await expect(page.locator('id=alert-success')).toBeVisible();

        // Agora encontra a turma recém criada para editar
        // Vamos pegar o href do primeiro botão editar que bate com a série 9
        const editLink = page.locator('text=✏️ Editar').first();
        await editLink.click();

        await expect(page.locator('h2')).toContainText('Editar Turma');

        // Altera a série para 8
        await page.fill('input[name="serie"]', '8');
        await page.click('id=btn-salvar-turma');

        // Deve redirecionar para 'turmas.show'
        await expect(page.locator('h2')).toContainText('Detalhes da Turma');
        await expect(page.locator('h2')).toContainText('8º E2E');

        // Voltar para a lista e desativar
        await page.goto('/turmas');
        
        // Clica em desativar na primeira turma
        // Como o confirm nativo trava o playwright, aceitamos automaticamente:
        page.once('dialog', dialog => dialog.accept());
        
        await page.click('button:has-text("🚫 Desativar")');
        
        // Verifica mensagem de reativar
        await expect(page.locator('id=alert-success')).toBeVisible();
        await expect(page.locator('button:has-text("✅ Reativar")')).toBeVisible();
    });
});
