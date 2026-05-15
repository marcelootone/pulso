import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Importação de Alunos', () => {
    test.beforeEach(async ({ page }) => {
        // Supondo que a aplicação tem um login padrão
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@sigae.com');
        await page.fill('input[name="password"]', 'password'); // Senha comum para testes
        await page.click('button[type="submit"]');
        await page.waitForURL('/dashboard');
    });

    test('Deve importar uma planilha de alunos com sucesso', async ({ page }) => {
        // Criar uma planilha dummy para teste
        const dummyCsvContent = `ID,Nome,Data Nascimento,Sexo,Telefones
9991,Aluno Teste 1,01/01/2010,M,11999999999
9992,Aluno Teste 2,02/02/2010,F,11888888888
,,,,`; // Linha vazia no final
        const filePath = path.join(__dirname, 'dummy_alunos.csv');
        fs.writeFileSync(filePath, dummyCsvContent);

        // Acessar a página de importação
        await page.goto('/importar-alunos');
        await expect(page).toHaveURL('/importar-alunos');

        // Selecionar uma turma
        // Como o dropdown pode variar, selecionamos a primeira opção disponível que não seja vazia
        const turmaSelect = await page.locator('select[name="turma_id"]');
        const firstOptionValue = await turmaSelect.locator('option').nth(1).getAttribute('value');
        if (firstOptionValue) {
            await turmaSelect.selectOption(firstOptionValue);
        }

        // Fazer upload do arquivo
        await page.setInputFiles('input[name="arquivo_csv"]', filePath);

        // Submeter o formulário
        await page.click('button[type="submit"]');

        // Aguardar e verificar a mensagem de sucesso
        await expect(page.locator('.alert-success, .text-green-600')).toBeVisible({ timeout: 10000 });
        await expect(page.locator('.alert-success, .text-green-600')).toContainText('Estudantes importados com sucesso');

        // Limpar o arquivo dummy
        fs.unlinkSync(filePath);
    });
});
