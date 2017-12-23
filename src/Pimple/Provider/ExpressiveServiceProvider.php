<?php

declare(strict_types=1);

namespace Sergiors\Yard\Pimple\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;

final class ExpressiveServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container[Application::class] = function (Container $container) {
            return (new ApplicationFactory)($container);
        };
    }
}
