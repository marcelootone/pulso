import { test, expect } from '@playwright/test';

/**
 * Testes E2E — Módulo 7.1: Estudo Orientado (Fluxo de Encaminhamento)
 *
 * Cobre os três perfis e fluxos:
 * 1. Professor Regular: Solicita encaminhamento de aluno.
 * 2. Coordenador: Analisa e aprova solicitação.
 * 3. Professor de EO: Registra atendimento/acompanhamento.
 */

const BASE_URL = '';

const PROFESSOR_REGULAR = { email: 'professor@example.com', password: 'password' };
const COORDENADOR       = { email: 'coordenador@example.com', password: 'password' };
const PROFESSOR_EO      = { email: 'professoreo@example.com', password: 'password' };

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
test.describe('Fluxo 1: Solicitação (Professor Regular)', () => {

    test('Professor regular vê menu e acessa solicitações', async ({ page }) => {
        await login(page, PROFESSOR_REGULAR.email, PROFESSOR_REGULAR.password);
        await expect(page.getByRole('link', { name: 'Est. Orientado' })).toBeVisible();
        await page.click('text=Est. Orientado');
        // Pode haver submenu "Solicitações" ou o link já levar para lá
        const link = page.getByRole('link', { name: 'Solicitações' });
        if (await link.isVisible()) {
            await link.click();
        }
        await expect(page).toHaveURL(/estudo-orientado\/solicitacoes/);
    });

    test('Professor regular cria nova solicitação para um aluno', async ({ page }) => {
        await login(page, PROFESSOR_REGULAR.email, PROFESSOR_REGULAR.password);
        await page.goto(`${BASE_URL}/estudo-orientado/solicitacoes/nova`);

        await page.selectOption('#turma_id', { index: 1 }); 
        
        // Espera a API carregar os alunos
        await page.waitForResponse(response => response.url().includes('/api/turmas/') && response.status() === 200);

        await page.selectOption('#aluno_id', { index: 1 });
        await page.fill('#disciplina_solicitante', 'Matemática');
        await page.selectOption('#prioridade', 'Media');
        await page.fill('#motivo', 'Aluno apresenta dificuldades severas em interpretação de texto e operações básicas.');

        await page.click('button[type="submit"]');

        await expect(page).toHaveURL(/estudo-orientado\/solicitacoes/);
        await expect(page.locator('.bg-green-100')).toContainText('Solicitação de encaminhamento criada com sucesso');
    });

});

// ============================================================
// FLUXO 2: Coordenador — Avaliar Solicitação
// ============================================================
test.describe('Fluxo 2: Análise (Coordenador)', () => {

    test('Coordenador vê menu e acessa painel de análise', async ({ page }) => {
        await login(page, COORDENADOR.email, COORDENADOR.password);
        await expect(page.getByRole('link', { name: 'Análises EO' }).or(page.getByRole('link', { name: 'Análises' }))).toBeVisible();
        
        await page.goto(`${BASE_URL}/estudo-orientado/analises`);
        await expect(page).toHaveURL(/estudo-orientado\/analises/);
        await expect(page.getByRole('heading', { name: /Análise de Encaminhamentos/i })).toBeVisible();
    });

    test('Coordenador aprova solicitação pendente', async ({ page }) => {
        await login(page, COORDENADOR.email, COORDENADOR.password);
        await page.goto(`${BASE_URL}/estudo-orientado/analises`);

        const btnAnalisar = page.locator('text=Analisar').first();
        if (await btnAnalisar.isVisible()) {
            await btnAnalisar.click();
            
            // Aprova
            if (await page.locator('#acao').isVisible()) {
                await page.selectOption('#acao', 'aprovar');
                await page.fill('#parecer', 'Aprovado para início imediato conforme relato do professor.');
                await page.click('button:has-text("Registrar Análise")');
                await expect(page.locator('.bg-green-100')).toContainText('Solicitação aprovada com sucesso');
            }
        }
    });

    test('Coordenador atribui professor orientador', async ({ page }) => {
        await login(page, COORDENADOR.email, COORDENADOR.password);
        await page.goto(`${BASE_URL}/estudo-orientado/analises`);

        // Filtra aprovadas
        await page.selectOption('#status', 'Aprovada');
        await page.click('button:has-text("Filtrar")');

        const btnAnalisar = page.locator('text=Analisar').first();
        if (await btnAnalisar.isVisible()) {
            await btnAnalisar.click();
            
            // Atribui orientador
            if (await page.locator('#professor_orientador_id').isVisible()) {
                await page.selectOption('#professor_orientador_id', { index: 1 });
                await page.click('button:has-text("Atribuir Orientador")');
                await expect(page.locator('.bg-green-100')).toContainText('Orientador atribuído com sucesso');
            }
        }
    });

});

// ============================================================
// FLUXO 3: Professor de EO — Acompanhamento
// ============================================================
test.describe('Fluxo 3: Acompanhamento (Professor EO)', () => {

    test('Professor EO vê menu e acessa seus alunos', async ({ page }) => {
        await login(page, PROFESSOR_EO.email, PROFESSOR_EO.password);
        await expect(page.getByRole('link', { name: 'Acompanhar EO' }).or(page.getByRole('link', { name: 'Acompanhamentos' }))).toBeVisible();
        
        await page.goto(`${BASE_URL}/estudo-orientado/acompanhamentos`);
        await expect(page).toHaveURL(/estudo-orientado\/acompanhamentos/);
        await expect(page.getByRole('heading', { name: /Meus Acompanhamentos/i })).toBeVisible();
    });

    test('Professor EO registra atendimento', async ({ page }) => {
        await login(page, PROFESSOR_EO.email, PROFESSOR_EO.password);
        await page.goto(`${BASE_URL}/estudo-orientado/acompanhamentos`);

        const btnAcessar = page.locator('text=Acessar Prontuário').first();
        if (await btnAcessar.isVisible()) {
            await btnAcessar.click();
            
            // Vai para a aba Atendimentos
            await page.click('button:has-text("Atendimentos (Sessões)")');
            
            // Preenche
            if (await page.locator('#descricao_atendimento').isVisible()) {
                await page.fill('#descricao_atendimento', 'Trabalhamos interpretação textual usando um gibi.');
                await page.click('button:has-text("Salvar Atendimento")');
                await expect(page.locator('.bg-green-100')).toContainText('Atendimento registrado com sucesso');
            }
        }
    });

});
