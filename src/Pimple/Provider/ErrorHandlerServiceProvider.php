<?php

declare(strict_types=1);

namespace Sergiors\Yard\Pimple\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Expressive\Middleware\ErrorResponseGenerator;
use Zend\Expressive\Container\ErrorHandlerFactory;
use Zend\Expressive\Container\WhoopsErrorResponseGeneratorFactory;
use Zend\Expressive\Container\WhoopsFactory;
use Zend\Expressive\Container\WhoopsPageHandlerFactory;
use Zend\Expressive\Container\ErrorResponseGeneratorFactory;
use Sergiors\Yard\Logger\LoggingErrorListener;

final class ErrorHandlerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container[ErrorHandler::class] = function (Container $container) {
            return (new ErrorHandlerFactory)($container);
        };

        $container[ErrorResponseGenerator::class] = function (Container $container) {
            if (true === $container['debug']) {
                return (new WhoopsErrorResponseGeneratorFactory)($container);
            }
            
            return (new ErrorResponseGeneratorFactory)($container);
        };

        $container['Zend\Expressive\Whoops'] = function (Container $container) {
            return (new WhoopsFactory)($container);
        };

        $container['Zend\Expressive\WhoopsPageHandler'] = function (Container $container) {
            return (new WhoopsPageHandlerFactory)($container);
        };

        $container[ErrorHandler::class] = $container->extend(ErrorHandler::class,
            function (ErrorHandler $errorHandler) use ($container) {
                $errorHandler->attachListener(
                    new LoggingErrorListener($container[LoggerInterface::class])
                );

                return $errorHandler;
            }
        );
    }
}
