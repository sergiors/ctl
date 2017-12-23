<?php

use Zend\Expressive\Application;
use Zend\Stratigility\Middleware\ErrorHandler;
use Sergiors\Yard\Container;
use Sergiors\Yard\Pimple\Provider\ExpressiveServiceProvider;
use Sergiors\Yard\Pimple\Provider\FastRouteServiceProvider;
use Sergiors\Yard\Pimple\Provider\ErrorHandlerServiceProvider;
use Sergiors\Yard\Pimple\Provider\MonologServiceProvider;
use Sergiors\Yard\Pimple\Provider\DockerServiceProvider;
use Sergiors\Yard\Logger\MonologMiddleware;
use Sergiors\Yard\ContainerMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$rootDir = dirname(__DIR__);
$cacheDir = $rootDir . '/cache';

$container = new class($rootDir, $cacheDir) extends Container {
    public function registerProviders(): array
    {
        return [
            new ExpressiveServiceProvider,
            new FastRouteServiceProvider,
            new ErrorHandlerServiceProvider,
            new MonologServiceProvider,
            new DockerServiceProvider,
        ];
    }
};

$container['fastroute.cache_enabled'] = getenv('APP_ENV') === 'prod';
$container['fastroute.cache_file'] = $rootDir . '/cache/fastroute.php';
$container['monolog.logfile'] = getenv('LOGFILE') ?: 'php://stdout';

$container[ContainerMiddleware::class] = function (Container $container) {
    return new ContainerMiddleware($container);
};

/** @var Application $app */
$app = $container->get(Application::class);

$app->pipe(ErrorHandler::class);
$app->pipe(MonologMiddleware::class);
$app->pipe(ContainerMiddleware::class);

$app->pipeRoutingMiddleware();
$app->pipeDispatchMiddleware();

$app->get('/containers', \Sergiors\Yard\Controllers\Containers::class);
$app->get('/images', \Sergiors\Yard\Controllers\Images::class);

$app->run();

if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}
