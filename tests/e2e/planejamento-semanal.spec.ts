import { test, expect } from '@playwright/test';

/**
 * Teste funcional E2E: Planejamento Semanal
 *
 * Pré-requisitos:
 * - Servidor rodando em http://127.0.0.1:8000
 * - Usuário de teste cadastrado (email: test@sigae.com, senha: password)
 *   Caso não exista, ajuste as credenciais abaixo.
 */

const LOGIN_EMAIL = 'test@sigae.com';
const LOGIN_PASSWORD = 'password';

async function login(page) {
    await page.goto('/login');
    await page.fill('input[name="email"]', LOGIN_EMAIL);
    await page.fill('input[name="password"]', LOGIN_PASSWORD);
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard');
}

test.describe('Planejamento Semanal', () => {

    test.beforeEach(async ({ page }) => {
        await login(page);
    });

    test('deve acessar via botão no dashboard', async ({ page }) => {
        // Verifica que o botão existe no dashboard
        const btnPlanejamento = page.locator('#btn-planejamento-semanal');
        await expect(btnPlanejamento).toBeVisible();
        await expect(btnPlanejamento).toContainText('Planejamento Semanal');

        // Clica no botão
        await btnPlanejamento.click();

        // Verifica que foi redirecionado para a página correta
        await expect(page).toHaveURL(/planejamento-semanal/);

        // Verifica o título
        const titulo = page.locator('#titulo-planejamento');
        await expect(titulo).toContainText('Rotina de Planejamento Pedagógico');
    });

    test('deve exibir período da semana atual', async ({ page }) => {
        await page.goto('/planejamento-semanal');

        // Verifica que o indicador de período está visível
        const periodo = page.locator('#periodo-semana');
        await expect(periodo).toBeVisible();

        // Deve conter o formato dd/mm/yyyy até dd/mm/yyyy
        const texto = await periodo.textContent();
        expect(texto).toMatch(/\d{2}\/\d{2}\/\d{4}\s+até\s+\d{2}\/\d{2}\/\d{4}/);
    });

    test('deve exibir tabela com horários padrão e colunas dos dias', async ({ page }) => {
        await page.goto('/planejamento-semanal');

        const tabela = page.locator('#tabela-planejamento');
        await expect(tabela).toBeVisible();

        // Verifica as colunas dos dias
        await expect(tabela.locator('th')).toContainText(['Horário', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira']);

        // Verifica que existem linhas de horário (matutino + vespertino = 12 linhas)
        const linhas = tabela.locator('tbody tr[data-horario-id]');
        const count = await linhas.count();
        expect(count).toBeGreaterThanOrEqual(12);
    });

    test('deve editar tarefa, andamento e observação e salvar', async ({ page }) => {
        await page.goto('/planejamento-semanal');

        // Pega o primeiro horário visível
        const primeiraLinha = page.locator('tr[data-horario-id]').first();
        const horarioId = await primeiraLinha.getAttribute('data-horario-id');

        // Preenche a tarefa da segunda-feira
        const inputTarefa = page.locator(`#tarefa-${horarioId}-SEGUNDA`);
        await inputTarefa.fill('Planejamento PCA');

        // Seleciona andamento
        const selectAndamento = page.locator(`#andamento-${horarioId}-SEGUNDA`);
        await selectAndamento.selectOption('EM_ANDAMENTO');

        // Preenche observação
        const textareaObs = page.locator(`#observacao-${horarioId}-SEGUNDA`);
        await textareaObs.fill('Reunião com coordenação');

        // Salva
        await page.click('#btn-salvar');

        // Verifica mensagem de sucesso
        const msgSucesso = page.locator('#feedback-success');
        await expect(msgSucesso).toBeVisible();
        await expect(msgSucesso).toContainText('Alterações salvas com sucesso');
    });

    test('deve persistir dados após recarregar a página', async ({ page }) => {
        await page.goto('/planejamento-semanal');

        const primeiraLinha = page.locator('tr[data-horario-id]').first();
        const horarioId = await primeiraLinha.getAttribute('data-horario-id');

        // Preenche e salva
        await page.locator(`#tarefa-${horarioId}-TERCA`).fill('Tutoria Individual');
        await page.locator(`#andamento-${horarioId}-TERCA`).selectOption('CONCLUIDO');
        await page.click('#btn-salvar');
        await expect(page.locator('#feedback-success')).toBeVisible();

        // Recarrega
        await page.reload();

        // Verifica persistência
        const tarefaValue = await page.locator(`#tarefa-${horarioId}-TERCA`).inputValue();
        expect(tarefaValue).toBe('Tutoria Individual');

        const andamentoValue = await page.locator(`#andamento-${horarioId}-TERCA`).inputValue();
        expect(andamentoValue).toBe('CONCLUIDO');
    });

    test('deve adicionar novo horário com botão + Criar Horário', async ({ page }) => {
        await page.goto('/planejamento-semanal');

        // Conta linhas antes
        const linhasAntes = await page.locator('tr[data-horario-id]').count();

        // Abre dropdown
        await page.click('#btn-criar-horario');
        await expect(page.locator('#dropdown-criar-horario')).toBeVisible();

        // Define horários
        await page.fill('#novo-horario-inicio', '18:00');
        await page.fill('#novo-horario-fim', '18:50');

        // Confirma
        await page.click('#btn-confirmar-horario');

        // Espera recarregar
        await page.waitForURL(/planejamento-semanal/);

        // Verifica que foi adicionada uma linha
        const linhasDepois = await page.locator('tr[data-horario-id]').count();
        expect(linhasDepois).toBe(linhasAntes + 1);

        // Verifica mensagem de sucesso
        await expect(page.locator('#feedback-success')).toContainText('Horário adicionado com sucesso');
    });

    test('deve navegar para semana anterior e próxima', async ({ page }) => {
        await page.goto('/planejamento-semanal');

        // Captura período atual
        const periodoAtual = await page.locator('#periodo-semana').textContent();

        // Navega para semana anterior
        await page.click('#btn-semana-anterior');
        await page.waitForURL(/planejamento-semanal/);
        const periodoAnterior = await page.locator('#periodo-semana').textContent();
        expect(periodoAnterior).not.toBe(periodoAtual);

        // Navega para próxima (volta ao original)
        await page.click('#btn-proxima-semana');
        await page.waitForURL(/planejamento-semanal/);
        const periodoVoltou = await page.locator('#periodo-semana').textContent();
        expect(periodoVoltou?.trim()).toBe(periodoAtual?.trim());
    });

    test('deve remover um horário', async ({ page }) => {
        await page.goto('/planejamento-semanal');

        const linhasAntes = await page.locator('tr[data-horario-id]').count();

        // Aceita o confirm dialog
        page.on('dialog', dialog => dialog.accept());

        // Clica no botão remover da última linha
        const ultimaLinha = page.locator('tr[data-horario-id]').last();
        const horarioId = await ultimaLinha.getAttribute('data-horario-id');
        await page.click(`#btn-remover-${horarioId}`);

        await page.waitForURL(/planejamento-semanal/);

        // Verifica que foi removida
        const linhasDepois = await page.locator('tr[data-horario-id]').count();
        expect(linhasDepois).toBe(linhasAntes - 1);
    });

});
