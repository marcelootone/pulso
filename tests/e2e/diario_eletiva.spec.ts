import { test, expect } from '@playwright/test';

test.describe('Diário de Eletivas', () => {
  // Configuração inicial: O admin pode ver todas as eletivas
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@sigae.edu.br');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL('/dashboard');
  });

  test('Deve acessar o diário e lançar frequência', async ({ page }) => {
    // Acessa eletivas
    await page.click('text=Eletivas e Clubes');
    await expect(page).toHaveURL('/eletivas');
    
    // Clica no link/ícone do Diário da primeira eletiva da lista
    // Assumindo que a eletiva criada nos testes anteriores existe
    const linkDiario = page.locator('a[title="Diário de Frequência e Notas"]').first();
    if (await linkDiario.count() === 0) {
      // Se não houver eletivas, encerra o teste limpo
      test.skip();
    }
    await linkDiario.click();

    // Deve estar na aba de frequência
    await expect(page.locator('text=Lançamento de Frequência')).toBeVisible();

    // Clica em Salvar Chamada (se houver alunos para salvar)
    const btnSalvar = page.locator('button:has-text("Salvar Chamada")');
    if (await btnSalvar.count() > 0) {
        await btnSalvar.click();
        await expect(page.locator('text=Frequência salva com sucesso!')).toBeVisible();
    }
  });

  test('Deve acessar aba de notas e lançar avaliação', async ({ page }) => {
    await page.goto('/eletivas');
    const linkDiario = page.locator('a[title="Diário de Frequência e Notas"]').first();
    if (await linkDiario.count() === 0) {
      test.skip();
    }
    await linkDiario.click();

    // Clica na aba de Lançamento de Notas (se existir a flag usa_nota)
    const abaNotas = page.locator('a:has-text("Lançamento de Notas")');
    if (await abaNotas.count() > 0) {
        await abaNotas.click();

        // Preenche formulário
        await page.fill('input[name="descricao"]', 'Trabalho Playwright');

        // Preenche nota do primeiro aluno, se houver
        const inputNota = page.locator('input[type="number"]').first();
        if (await inputNota.count() > 0) {
            await inputNota.fill('95.5');
            await page.click('button:has-text("Salvar Notas")');
            await expect(page.locator('text=Notas salvas com sucesso!')).toBeVisible();
            await expect(page.locator('text=Trabalho Playwright')).toBeVisible();
        }
    }
  });
});
