import { test, expect } from '@playwright/test';

test.describe('Eletivas e Clubes', () => {
  // Configuração inicial para criar dados necessários (ou assumir que o banco de testes os tem)
  // Utilizaremos o Gestor que já deve estar criado pelo seeder padrão

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@sigae.edu.br');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL('/dashboard');
  });

  test('Deve criar uma nova eletiva com sucesso', async ({ page }) => {
    await page.click('text=Eletivas e Clubes');
    await expect(page).toHaveURL('/eletivas');
    
    await page.click('text=Nova Eletiva/Clube');
    await expect(page).toHaveURL('/eletivas/create');

    await page.fill('input[name="nome"]', 'Robótica Avançada');
    await page.selectOption('select[name="tipo"]', 'eletiva');
    await page.fill('input[name="vagas"]', '20');
    await page.fill('input[name="ano_letivo"]', '2026');
    await page.fill('textarea[name="descricao"]', 'Curso de robótica para o ensino médio');
    
    const selectProf = page.locator('select#professor_ids');
    if (await selectProf.count() > 0) {
        const option = await selectProf.locator('option').nth(1).getAttribute('value');
        if (option) {
            await selectProf.selectOption(option, { force: true });
        }
    }

    await page.click('button[type="submit"]');

    // Verifica redirecionamento e mensagem de sucesso
    await expect(page).toHaveURL('/eletivas');
    await expect(page.locator('text=Registro criado com sucesso!')).toBeVisible();
    await expect(page.locator('text=Robótica Avançada')).toBeVisible();
  });

  test('Deve visualizar detalhes de uma eletiva', async ({ page }) => {
    await page.goto('/eletivas');
    
    // Clica no link 'Ver' do primeiro item
    await page.click('text=Ver');
    
    await expect(page.locator('h3:has-text("Informações")')).toBeVisible();
    await expect(page.locator('h3:has-text("Professores Responsáveis")')).toBeVisible();
    await expect(page.locator('h3:has-text("Alunos Inscritos")')).toBeVisible();
  });
});
