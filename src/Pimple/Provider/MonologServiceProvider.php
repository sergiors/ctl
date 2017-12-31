<?php

declare(strict_types=1);

namespace Sergiors\Ctl\Pimple\Provider;

use Monolog\Formatter\JsonFormatter;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Sergiors\Ctl\Logger\MonologMiddleware;
use InvalidArgumentException;

final class MonologServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['monolog.name'] = 'app';
        $container['monolog.bubble'] = true;

        $container['monolog.formatter'] = function () {
            return new JsonFormatter;
        };

        $container['monolog.handlers'] = function (Container $container) {
            $level = self::translateLevel(getenv('LOGLEVEL') ?: Logger::DEBUG);

            $handlers = [
                (new StreamHandler(
                    getenv('LOGFILE') ?: 'php://stdout',
                    $level
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

    public static function translateLevel($name): int
    {
        if (is_int($name)) {
            return $name;
        }

        $levels = Logger::getLevels();
        $upper = strtoupper($name);

        if (!isset($levels[$upper])) {
            throw new InvalidArgumentException(
                "Provided logging level '$name' does not exist. Must be a valid monolog logging level."
            );
        }

        return $levels[$upper];
    }
}
