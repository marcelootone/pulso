import { test, expect } from '@playwright/test';

/**
 * Testes E2E — Módulo 7.1: Estudo Orientado
 *
 * Cobre os dois fluxos principais:
 * 1. Professor Regular: cria solicitação de atividade de EO
 * 2. Professor de Estudo Orientado: avalia a atividade (checklist de alunos)
 */

const BASE_URL = 'http://sigae.test';

// Credenciais (ajuste conforme os usuários de teste no seed)
const PROFESSOR_REGULAR = { email: 'professor@sigae.test', password: 'password' };
const PROFESSOR_EO      = { email: 'professoreo@sigae.test', password: 'password' };

async function login(page, email: string, password: string) {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', password);
    await page.click('button[type="submit"]');
    await page.waitForURL(`${BASE_URL}/dashboard`);
}

// ============================================================
// FLUXO 1: Professor Regular — Criar Solicitação
// ============================================================
test.describe('Fluxo 1: Solicitação de Estudo Orientado (Professor Regular)', () => {

    test('Professor regular vê link "Est. Orientado" no menu', async ({ page }) => {
        await login(page, PROFESSOR_REGULAR.email, PROFESSOR_REGULAR.password);
        await expect(page.getByRole('link', { name: 'Est. Orientado' })).toBeVisible();
    });

    test('Professor regular acessa listagem de solicitações', async ({ page }) => {
        await login(page, PROFESSOR_REGULAR.email, PROFESSOR_REGULAR.password);
        await page.click('text=Est. Orientado');
        await expect(page).toHaveURL(/estudo-orientado\/solicitacoes/);
        await expect(page.getByRole('heading', { name: /Estudo Orientado/i })).toBeVisible();
    });

    test('Professor regular cria nova solicitação com sucesso', async ({ page }) => {
        await login(page, PROFESSOR_REGULAR.email, PROFESSOR_REGULAR.password);
        await page.goto(`${BASE_URL}/estudo-orientado/solicitacoes/nova`);

        // Preenche o formulário
        await page.selectOption('#turma_id', { index: 1 }); // Primeira turma disponível
        await page.fill('#disciplina_solicitante', 'Matemática');
        await page.fill('#data_prevista', new Date(Date.now() + 86400000).toISOString().split('T')[0]); // amanhã
        await page.fill('#descricao', 'Resolver as questões 1 a 10 da página 45 do livro de Matemática sobre equações de 1° grau.');

        await page.click('button[type="submit"]');

        // Verifica redirecionamento e mensagem de sucesso
        await expect(page).toHaveURL(/estudo-orientado\/solicitacoes/);
        await expect(page.locator('.bg-green-100')).toContainText('Solicitação de Estudo Orientado criada com sucesso');
    });

    test('Formulário valida campos obrigatórios', async ({ page }) => {
        await login(page, PROFESSOR_REGULAR.email, PROFESSOR_REGULAR.password);
        await page.goto(`${BASE_URL}/estudo-orientado/solicitacoes/nova`);

        // Tenta submeter vazio
        await page.click('button[type="submit"]');

        // Verifica que a página não redirecionou (formulário HTML5 ou mensagens de erro)
        await expect(page).toHaveURL(/solicitacoes\/nova/);
    });
});

// ============================================================
// FLUXO 2: Professor de EO — Avaliar Atividade
// ============================================================
test.describe('Fluxo 2: Avaliação de Estudo Orientado (Professor de EO)', () => {

    test('Professor de EO vê link "Avaliar EO" no menu', async ({ page }) => {
        await login(page, PROFESSOR_EO.email, PROFESSOR_EO.password);
        await expect(page.getByRole('link', { name: 'Avaliar EO' })).toBeVisible();
    });

    test('Professor de EO acessa painel de avaliações', async ({ page }) => {
        await login(page, PROFESSOR_EO.email, PROFESSOR_EO.password);
        await page.click('text=Avaliar EO');
        await expect(page).toHaveURL(/estudo-orientado\/avaliacoes/);
        await expect(page.getByRole('heading', { name: /Estudo Orientado/i })).toBeVisible();
    });

    test('Professor de EO não consegue acessar a criação de solicitação', async ({ page }) => {
        await login(page, PROFESSOR_EO.email, PROFESSOR_EO.password);
        await page.goto(`${BASE_URL}/estudo-orientado/solicitacoes/nova`);
        // Deve ser bloqueado (403 ou redirecionamento)
        await expect(page).not.toHaveURL(/solicitacoes\/nova/);
    });

    test('Botão "Marcar Todos como Cumpriram" funciona na tela de avaliação', async ({ page }) => {
        await login(page, PROFESSOR_EO.email, PROFESSOR_EO.password);
        await page.goto(`${BASE_URL}/estudo-orientado/avaliacoes`);

        // Se houver uma atividade pendente, clica em Avaliar Alunos
        const btnAvaliar = page.locator('text=▶ Avaliar Alunos').first();
        if (await btnAvaliar.isVisible()) {
            await btnAvaliar.click();
            await expect(page).toHaveURL(/estudo-orientado\/avaliacoes\/\d+/);

            // Clica em marcar todos
            await page.click('#btn-marcar-todos');

            // Verifica que todos checkboxes estão marcados
            const checkboxes = page.locator('.aluno-checkbox');
            const count = await checkboxes.count();
            for (let i = 0; i < count; i++) {
                await expect(checkboxes.nth(i)).toBeChecked();
            }

            // Salva a avaliação
            await page.click('button[type="submit"]');
            await expect(page).toHaveURL(/estudo-orientado\/avaliacoes$/);
            await expect(page.locator('.bg-green-100')).toContainText('Avaliação salva com sucesso');
        }
    });
});
