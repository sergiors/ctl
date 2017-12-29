<?php

use Zend\Expressive\Application;
use Zend\Stratigility\Middleware\ErrorHandler;
use Sergiors\Ctl\Container;
use Sergiors\Ctl\Pimple\Provider\ExpressiveServiceProvider;
use Sergiors\Ctl\Pimple\Provider\FastRouteServiceProvider;
use Sergiors\Ctl\Pimple\Provider\ErrorHandlerServiceProvider;
use Sergiors\Ctl\Pimple\Provider\MonologServiceProvider;
use Sergiors\Ctl\Pimple\Provider\DockerServiceProvider;
use Sergiors\Ctl\Logger\MonologMiddleware;
use Sergiors\Ctl\ContainerMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$rootDir = dirname(__DIR__);
$cacheDir = $rootDir . '/cache';

chdir($rootDir);

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

$app->get('/containers', \Sergiors\Ctl\Controllers\Containers::class);
$app->get('/images', \Sergiors\Ctl\Controllers\Images::class);
$app->get('/errors', \Sergiors\Ctl\Controllers\Errors::class);

$app->run();

if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}
