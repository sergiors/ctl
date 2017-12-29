<?php

declare(strict_types=1);

namespace Sergiors\Ctl;

abstract class Container extends \Xtreamwayz\Pimple\Container
{
    public function __construct(string $rootDir, string $cacheDir)
    {
        parent::__construct([
            'rootDir'  => $rootDir,
            'cacheDir' => $cacheDir,
            'debug'    => getenv('APP_ENV') ?: 'dev' === 'dev',
        ]);

        $this->initializeDotenv($rootDir);
        $this->initializeProviders();
    }

    private function initializeDotenv(string $path): void
    {
        (
            new \Dotenv\Dotenv($path)
        )->load();
    }

    private function initializeProviders(): void
    {
        $providers = $this->registerProviders();

        foreach ($providers as $provider) {
            $this->register($provider);
        }
    }

    abstract public function registerProviders(): array;
}
