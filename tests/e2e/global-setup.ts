import { execSync } from 'node:child_process';

/**
 * Global setup do Playwright.
 *
 * Antes de qualquer teste, popula o banco com dados determinísticos (E2ETestSeeder):
 * administrador de teste, turma ativa e estudante infrequente. O seeder é idempotente
 * (usa updateOrCreate / firstOrCreate), portanto NÃO apaga dados existentes do ambiente.
 *
 * Caso prefira um banco isolado, defina APP_ENV/DB_* em um arquivo .env.testing e rode
 * `php artisan migrate:fresh --seed --seeder=E2ETestSeeder` apontando para ele.
 */
export default async function globalSetup() {
    console.log('[global-setup] Populando o banco com E2ETestSeeder...');
    execSync('php artisan db:seed --class=E2ETestSeeder --force', {
        stdio: 'inherit',
    });
    console.log('[global-setup] Seed concluído.');
}
