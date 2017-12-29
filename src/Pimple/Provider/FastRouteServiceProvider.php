<?php

declare(strict_types=1);

namespace Sergiors\Ctl\Pimple\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\FastRouteRouter;

final class FastRouteServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['fastroute.cache_file'] = function (Container $container) {
            return $container['cacheDir'] . '/fastroute.php.cache';
        };

        $container[RouterInterface::class] = function (Container $container) {
            return new FastRouteRouter(null, null, [
                FastRouteRouter::CONFIG_CACHE_ENABLED => getenv('APP_ENV') === 'prod',
                FastRouteRouter::CONFIG_CACHE_FILE    => $container['fastroute.cache_file'],
            ]);
        };
    }
}
