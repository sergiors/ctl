<?php

declare(strict_types=1);

namespace Sergiors\Yard\Pimple\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Sergiors\Yard\Logger\MonologMiddleware;

final class MonologServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['monolog.name'] = 'app';
        $container['monolog.bubble'] = true;
        $container['monolog.logfile'] = 'php://stdout';

        $container['monolog.level'] = function () {
            return Logger::DEBUG;
        };

        $container['monolog.formatter'] = function () {
            return new LineFormatter;
        };

        $container['monolog.handlers'] = function (Container $container) {
            $handlers = [
                (new StreamHandler(
                    $container['monolog.logfile'],
                    $container['monolog.level']
                ))->setFormatter($container['monolog.formatter'])
            ];

            return $handlers;
        };


        $container[LoggerInterface::class] = function (Container $container) {
            return new Logger(
                $container['monolog.name'],
                $container['monolog.handlers']
            );
        };

        $container[MonologMiddleware::class] = function (Container $container) {
            return new MonologMiddleware($container[LoggerInterface::class]);
        };
    }
}
