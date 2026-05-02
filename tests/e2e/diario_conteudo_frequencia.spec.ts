import { test, expect } from '@playwright/test';

test.describe('Sprint 2: Gestão de Conteúdos e Frequência', () => {

    test('Deve validar o registro do Diário (Presenças e Conteúdos Ministrados)', async ({ page }) => {
        // Assume login prévio
        await page.goto('/login');
        await page.fill('input[name="email"]', 'professor@sigae.com'); // Exemplo
        await page.fill('input[name="password"]', 'senha123');
        await page.click('button[type="submit"]');

        // Acesso ao Diário (supondo turma ID 1)
        await page.goto('/meu-diario/1');

        // Validar a presença da seção de Conteúdo Ministrado
        const tituloConteudo = page.locator('text=Conteúdo Ministrado');
        await expect(tituloConteudo).toBeVisible();

        const textareaAula1 = page.locator('textarea[name="conteudos[1]"]');
        await expect(textareaAula1).toBeVisible();

        const textareaAula2 = page.locator('textarea[name="conteudos[2]"]');
        await expect(textareaAula2).toBeVisible();

        // Simula preenchimento dos conteúdos
        await textareaAula1.fill('Introdução à Lógica de Programação');
        await textareaAula2.fill('Exercícios Práticos de Variáveis e Tipos de Dados');

        // Simula o registro de frequência dos alunos presentes
        // Aqui assumimos que todos estão como P por padrão, ou marcamos o primeiro como Falta
        const primeiroFalta = page.locator('input[value="F"]').first();
        if (await primeiroFalta.count() > 0) {
            await primeiroFalta.check();
        }

        // Submete o formulário
        await page.click('button:has-text("SALVAR DIÁRIO")');

        // Validar mensagem de sucesso
        await expect(page.locator('text=Chamada e conteúdo salvos com sucesso!')).toBeVisible();

        // Validar se redirecionou para o índice do diário
        await expect(page).toHaveURL(/\/meu-diario/);
    });

});
