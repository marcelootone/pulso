import { test, expect } from '@playwright/test';

test.describe('Enriquecimento de Base: Turmas e Alunos', () => {

    test('Deve validar o formulário de criação de Turma com Modalidade e Turno', async ({ page }) => {
        // Assume login prévio ou acesso direto se as rotas estiverem liberadas para teste
        await page.goto('/login');
        await page.fill('input[name="email"]', 'andre@email.com');
        await page.fill('input[name="password"]', 'andre123456');
        await page.click('button[type="submit"]');
        
        await expect(page).toHaveURL('/dashboard');

        // Acessar a tela de criação de turmas
        await page.goto('/turmas/create');

        // Validar a presença dos selects Modalidade e Turno
        const modalidadeSelect = page.locator('select[name="modalidade"]');
        await expect(modalidadeSelect).toBeVisible();

        const turnoSelect = page.locator('select[name="turno"]');
        await expect(turnoSelect).toBeVisible();

        // Preencher o formulário
        await modalidadeSelect.selectOption({ value: 'EM - Ensino Médio' });
        await turnoSelect.selectOption({ value: 'Matutino' });
        await page.fill('input[name="serie"]', '1');
        await page.fill('input[name="complemento"]', 'A');

        // Submeter
        await page.click('button[type="submit"]');

        // Validar sucesso
        await expect(page.locator('text=Turma criada com sucesso!')).toBeVisible();
    });

    test('Deve validar a tela de importação de Alunos', async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', 'andre@email.com');
        await page.fill('input[name="password"]', 'andre123456');
        await page.click('button[type="submit"]');

        // Navegar para a tela de importação
        await page.goto('/importacao/create');

        // Validar que o form de importação carrega perfeitamente (onde adicionamos a lógica estendida no backend)
        await expect(page.locator('form[action*="importacao"]')).toBeVisible();
        await expect(page.locator('input[type="file"][name="arquivo_csv"]')).toBeVisible();
        await expect(page.locator('select[name="turma_id"]')).toBeVisible();
        
        // Simulação básica da interface (não submetemos um arquivo real pois testar upload de excel requer arquivo mock físico)
    });
});
