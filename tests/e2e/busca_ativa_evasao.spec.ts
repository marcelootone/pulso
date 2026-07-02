import { test, expect } from '@playwright/test';

/**
 * Teste E2E do fluxo crítico de Busca Ativa / Registro de evasão (Seção 4.1.6 do TCC).
 *
 * Pré-condição: o banco foi populado pelo E2ETestSeeder (ver global-setup.ts), que cria:
 *  - admin@example.com / password (perfil Administrador);
 *  - "Aluno Teste E2E" (RA E2E0001) com faltas suficientes para ser sinalizado
 *    como infrequente pelas janelas deslizantes da SEDU-ES 2026.
 *
 * O teste comprova, de ponta a ponta:
 *  1. que a detecção por janelas deslizantes lista o estudante infrequente no painel;
 *  2. que a transição de status para "Deixou de frequentar" persiste no banco e
 *     gera, de forma automática, o registro de alerta na Busca Ativa;
 *  3. que esse registro automático é exibido imediatamente no painel.
 */

const ALUNO_NOME = 'Aluno Teste E2E';
const ALUNO_RA = 'E2E0001';

test.describe('Busca Ativa - Evasão Escolar', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@example.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
    });

    test('detecta estudante infrequente, registra evasão e exibe alerta automático', async ({ page }) => {
        // 1. DETECÇÃO: o aluno com faltas deve aparecer no painel de Busca Ativa.
        await page.goto('/frequencia/busca-ativa');
        await expect(page.locator('h3', { hasText: 'Estudantes Infrequentes' })).toBeVisible();
        await expect(page.getByText(ALUNO_NOME, { exact: false })).toBeVisible();

        // 2. EVASÃO: localizar o aluno e alterar o status para "Deixou de frequentar".
        await page.goto(`/alunos?search=${ALUNO_RA}`);
        const linha = page.locator('tr', { hasText: ALUNO_NOME });
        await expect(linha).toBeVisible();
        await linha.getByRole('link', { name: 'Editar' }).click();
        await expect(page).toHaveURL(/.*\/alunos\/\d+\/edit/);

        await page.selectOption('select[name="status_matricula"]', 'Deixou de frequentar');
        await page.click('button:has-text("Salvar Alterações")');

        // Persistência confirmada pela mensagem de sucesso após o redirect.
        await expect(page.getByText('Aluno atualizado com sucesso!')).toBeVisible();

        // 3. ALERTA AUTOMÁTICO: o registro inserido automaticamente em
        //    busca_ativa_registros deve aparecer no histórico do painel.
        await page.goto('/frequencia/busca-ativa');
        await expect(page.getByText(ALUNO_NOME, { exact: false })).toBeVisible();
        await expect(page.getByText('gerado automaticamente', { exact: false })).toBeVisible();
    });
});
