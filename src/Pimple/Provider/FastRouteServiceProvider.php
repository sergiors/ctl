<?php

declare(strict_types=1);

namespace Sergiors\Yard\Pimple\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\FastRouteRouter;

final class FastRouteServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['fastroute.cache_enabled'] = false;
        $container['fastroute.cache_file'] = null;

        $container[RouterInterface::class] = function (Container $container) {
            return new FastRouteRouter(null, null, [
                FastRouteRouter::CONFIG_CACHE_ENABLED => $container['fastroute.cache_enabled'],
                FastRouteRouter::CONFIG_CACHE_FILE    => $container['fastroute.cache_file'],
            ]);
        };
    }
}
