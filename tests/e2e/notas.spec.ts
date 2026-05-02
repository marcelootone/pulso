import { test, expect } from '@playwright/test';

test.describe('Lançamento de Notas por Avaliação', () => {
    test('Deve criar uma avaliação e lançar as notas', async ({ page }) => {
        // Assume que já existe um usuário e uma turma com id 1 e a disciplina Matemática
        await page.goto('/login');
        await page.fill('input[name="email"]', 'andre@email.com'); // Ajuste de acordo com o usuário real de teste
        await page.fill('input[name="password"]', 'andre123456');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL('/dashboard');

        // Passo 2: Acessar a tela de avaliações da disciplina
        await page.goto('/turmas/1/disciplinas/Matematica/avaliacoes');

        // Passo 3: Criar uma nova avaliação
        await page.fill('input[name="nome"]', 'Prova Bimestral de Teste');
        await page.fill('input[name="periodo"]', '1º Bimestre');
        await page.fill('input[name="valor_maximo"]', '10.0');
        await page.click('button:has-text("Criar Avaliação")');

        // Verifica a mensagem de sucesso
        await expect(page.locator('text=Avaliação criada com sucesso!')).toBeVisible();

        // Passo 4: Entrar na tela de Lançar Notas da avaliação recém-criada
        await page.click('a:has-text("Lançar Notas")');

        // Passo 5: Preencher notas no formulário
        const noteInput = page.locator('input[name^="notas["]').first();
        const hasStudents = await noteInput.count() > 0;

        if (hasStudents) {
            await noteInput.fill('9.5');
            
            // Submeter o formulário
            await page.click('button:has-text("Salvar Notas")');

            // Validar a mensagem de sucesso
            const successMessage = page.locator('text=Notas salvas com sucesso!');
            await expect(successMessage).toBeVisible();

            // Valida se o valor salvo persiste no input
            await expect(noteInput).toHaveValue('9.5');
        } else {
            await expect(page.locator('text=Nenhum aluno cadastrado nesta turma.')).toBeVisible();
        }
    });
});
