import { test, expect } from '@playwright/test';

test.describe('Sprint 3: Agendamento de Espaços', () => {

    test('Deve acessar os espaços, reservar e bloquear duplicidade', async ({ page }) => {
        // Assume login prévio
        await page.goto('/login');
        await page.fill('input[name="email"]', 'professor@sigae.com'); // Exemplo
        await page.fill('input[name="password"]', 'senha123');
        await page.click('button[type="submit"]');

        // Acessa a página de Agendamentos
        await page.goto('/agendamentos');

        // Garante que o card "Laboratório de Ciências" apareceu na tela (inserido pelo Seeder)
        await expect(page.locator('text=Laboratório de Ciências')).toBeVisible();

        // Clica no primeiro botão "Abrir / Reservar" (o laboratório tem o ID 1 geralmente)
        await page.click('a:has-text("Abrir / Reservar") >> nth=0');

        // Estamos na página de criar reserva do espaço
        await expect(page.locator('text=Efetuar Nova Reserva')).toBeVisible();

        // Faz uma reserva das 10:00 às 11:00
        await page.fill('input[name="horario_inicio"]', '10:00');
        await page.fill('input[name="horario_fim"]', '11:00');
        await page.fill('input[name="motivo"]', 'Aula de Biologia');
        await page.click('button:has-text("SALVAR RESERVA")');

        // Valida sucesso
        await expect(page.locator('text=Reserva efetuada com sucesso!')).toBeVisible();

        // Tenta fazer outra reserva no MESMO HORÁRIO para testar o bloqueio de concorrência
        await page.fill('input[name="horario_inicio"]', '10:30'); // Sobreposição
        await page.fill('input[name="horario_fim"]', '11:30');
        await page.fill('input[name="motivo"]', 'Teste Falho');
        await page.click('button:has-text("SALVAR RESERVA")');

        // Valida bloqueio de conflito
        await expect(page.locator('text=Este horário colide com uma reserva já existente.')).toBeVisible();
    });

});
