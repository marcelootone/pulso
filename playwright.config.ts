import { defineConfig, devices } from '@playwright/test';

/**
 * Read environment variables from file.
 * https://github.com/motdotla/dotenv
 */
// import dotenv from 'dotenv';
// import path from 'path';
// dotenv.config({ path: path.resolve(__dirname, '.env') });

/**
 * See https://playwright.dev/docs/test-configuration.
 */
export default defineConfig({
  testDir: './tests/e2e',
  /* Popula o banco (E2ETestSeeder) antes de iniciar a suíte. */
  globalSetup: './tests/e2e/global-setup.ts',
  /* IMPORTANTE: o servidor de testes (`php artisan serve`) é SINGLE-PROCESS.
     Sob paralelismo ele afoga (a requisição da página espera por uma sub-requisição
     que o mesmo processo, ocupado, não responde) → timeouts em massa.
     Por isso a suíte roda SERIALIZADA (1 worker, sem paralelismo intra-arquivo).
     Se você rodar contra o Laragon (sigae.test, com PHP-FPM concorrente) pode
     reativar o paralelismo: fullyParallel: true e workers: undefined. */
  fullyParallel: false,
  /* Fail the build on CI if you accidentally left test.only in the source code. */
  forbidOnly: !!process.env.CI,
  /* Retry on CI only */
  retries: process.env.CI ? 2 : 0,
  /* 1 worker = execução serial, compatível com o servidor single-process. */
  workers: 1,
  /* Reporter to use. See https://playwright.dev/docs/test-reporters */
  reporter: 'html',
  /* Shared settings for all the projects below. See https://playwright.dev/docs/api/class-testoptions. */
  use: {
    /* Base URL to use in actions like `await page.goto('')`. */
    baseURL: 'http://127.0.0.1:8000',

    /* Collect trace when retrying the failed test. See https://playwright.dev/docs/trace-viewer */
    trace: 'on-first-retry',
  },

  /* Configure projects for major browsers */
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },

  /*  {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
    },

    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
    },

    /* Test against mobile viewports. */
    // {
    //   name: 'Mobile Chrome',
    //   use: { ...devices['Pixel 5'] },
    // },
    // {
    //   name: 'Mobile Safari',
    //   use: { ...devices['iPhone 12'] },
    // },

    /* Test against branded browsers. */
    // {
    //   name: 'Microsoft Edge',
    //   use: { ...devices['Desktop Edge'], channel: 'msedge' },
    // },
    // {
    //   name: 'Google Chrome',
    //   use: { ...devices['Desktop Chrome'], channel: 'chrome' },
    // },
  ],

  /* Sobe o servidor Laravel automaticamente antes dos testes.
     reuseExistingServer evita conflito caso o Laragon/artisan já esteja servindo em 8000. */
  webServer: {
    command: 'php artisan serve --host=127.0.0.1 --port=8000',
	url: 'http://127.0.0.1:8000',
    reuseExistingServer: true,
    timeout: 120 * 1000,
  },
});
