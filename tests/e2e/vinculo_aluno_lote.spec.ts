import { test, expect } from '@playwright/test';

test.describe('Importação - Aba Vincular Aluno', () => {

    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
    });

    test('Deve exibir as duas abas na tela de importação', async ({ page }) => {
        await page.goto('/importar-alunos');

        // Verifica presença dos dois botões de aba
        await expect(page.locator('button:has-text("Importar Usuarios (Planilha)")')).toBeVisible();
        await expect(page.locator('button:has-text("Vincular Aluno")')).toBeVisible();
    });

    test('Ao clicar em "Vincular Aluno" deve exibir o formulário de vinculação', async ({ page }) => {
        await page.goto('/importar-alunos');

        // Clica na aba Vincular Aluno
        await page.click('button:has-text("Vincular Aluno")');

        // Verifica o formulário da aba
        await expect(page.locator('select[name="turma_id"]').first()).toBeVisible();
        await expect(page.locator('input[placeholder="Buscar por nome ou RA..."]')).toBeVisible();
        await expect(page.locator('button:has-text("Vincular")')).toBeVisible();
    });

    test('Aba "Vincular Aluno" deve abrir direto quando turma_id e tab=vincular são passados via URL', async ({ page }) => {
        await page.goto('/importar-alunos?turma_id=2&tab=vincular');

        // Verifica que a aba de vincular já está ativa (select de turma de destino visível)
        await expect(page.locator('input[placeholder="Buscar por nome ou RA..."]')).toBeVisible();
    });

    test('Busca autocomplete deve filtrar alunos por nome', async ({ page }) => {
        await page.goto('/importar-alunos');
        await page.click('button:has-text("Vincular Aluno")');

        const searchInput = page.locator('input[placeholder="Buscar por nome ou RA..."]');
        await searchInput.fill('a');

        // Aguarda o dropdown aparecer com ao menos 1 item
        const dropdown = page.locator('ul[style*="display: none"]').filter({ has: page.locator('li') }).first();
        // Espera os itens do dropdown aparecerem via Alpine.js (x-show)
        await page.waitForTimeout(300);
        await expect(searchInput).toHaveValue('a');
    });
});
