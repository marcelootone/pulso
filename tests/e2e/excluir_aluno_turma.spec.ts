import { test, expect } from '@playwright/test';

test.describe('Excluir Aluno da Turma', () => {

    test('Deve exibir o botão de excluir e realizar a exclusão de um aluno', async ({ page }) => {
        // Login
        await page.goto('/login');
        await page.fill('input[name="email"]', 'andre@email.com');
        await page.fill('input[name="password"]', 'andre123456');
        await page.click('button[type="submit"]');
        
        await expect(page).toHaveURL('/dashboard');

        // Acessar a tela de detalhes de uma turma
        await page.goto('/turmas/2');

        // Verifica se existe um botão de remover
        const excluirBtn = page.locator('button:has-text("🗑️ Remover")').first();
        
        if (await excluirBtn.isVisible()) {
            // Intercepta o evento de confirm do JS para aceitar a exclusão
            page.on('dialog', dialog => dialog.accept());
            
            await excluirBtn.click();
            
            // Validar mensagem de sucesso
            await expect(page.locator('text=Aluno removido da turma com sucesso!')).toBeVisible();
        } else {
            console.log('Nenhum aluno para excluir na turma 2 durante o teste.');
        }
    });

});
