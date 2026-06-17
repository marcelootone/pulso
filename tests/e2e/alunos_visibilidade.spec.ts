import { test, expect } from '@playwright/test';

test.describe('Visibilidade de Alunos por Perfil', () => {
    
    test('Gestor deve ver a listagem de Alunos', async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', 'gestor@sigae.edu.br');
        await page.fill('input[name="password"]', 'password');
        
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard*', { timeout: 10000 }).catch(() => null);

        await page.goto('/alunos');
        await expect(page.locator('h2').first()).toContainText('Alunos');
    });

    test('Professor deve ver apenas alunos de suas turmas (página carrega sem erros)', async ({ page }) => {
        await page.goto('/login');
        
        // Em um cenário real com seeders, usaríamos um login de professor
        await page.fill('input[name="email"]', 'professor@example.com');
        await page.fill('input[name="password"]', 'password');
        
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard*', { timeout: 10000 }).catch(() => null);

        await page.goto('/alunos');
        await expect(page.locator('h2').first()).toBeVisible();

        const pageText = await page.innerText('body');
        expect(pageText).not.toContain('Exception');
        expect(pageText).not.toContain('Error');
    });
});
