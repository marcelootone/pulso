import { test, expect } from '@playwright/test';

test.describe('Vinculação de Aluno a Turma', () => {
    test.beforeEach(async ({ page }) => {
        // Fazer login antes de acessar a página
        await page.goto('http://sigae.test/login');
        await page.fill('#email', 'admin@sigae.com'); // Substitua pelo usuário de teste correto
        await page.fill('#password', 'password'); // Substitua pela senha correta
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL(/.*dashboard/);
    });

    test('Deve renderizar o formulário de vinculação com selects', async ({ page }) => {
        await page.goto('http://sigae.test/vinculo-aluno-turma');
        await expect(page.locator('h2')).toContainText('Vincular Aluno a Turma');
        await expect(page.locator('select#aluno_id')).toBeVisible();
        await expect(page.locator('select#turma_id')).toBeVisible();
    });

    test('Deve conseguir selecionar aluno e turma e salvar', async ({ page }) => {
        await page.goto('http://sigae.test/vinculo-aluno-turma');

        // Selecionar o primeiro aluno disponível
        const alunoSelect = page.locator('select#aluno_id');
        await alunoSelect.selectOption({ index: 1 });

        // Selecionar a primeira turma disponível
        const turmaSelect = page.locator('select#turma_id');
        await turmaSelect.selectOption({ index: 1 });

        // Selecionar o tipo de vínculo (ELETIVA) para não conflitar caso ele já tenha uma REGULAR
        const tipoSelect = page.locator('select#tipo_vinculo');
        await tipoSelect.selectOption({ value: 'ELETIVA' });

        // Clicar no botão de vincular
        await page.click('button:has-text("Vincular Aluno")');

        // Verificar a mensagem de sucesso
        await expect(page.locator('.bg-green-100')).toContainText('Aluno vinculado à turma com sucesso!');
    });
});
