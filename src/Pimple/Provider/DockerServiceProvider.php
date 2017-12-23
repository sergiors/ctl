<?php

declare(strict_types=1);

namespace Sergiors\Yard\Pimple\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Docker\Docker;
use Docker\DockerClient as Client;

final class DockerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['docker.options'] = [
            'remote_socket' => 'unix:///var/run/docker.sock',
        ];

        $container[Docker::class] = function (Container $container) {
            return new Docker(
                new Client($container['docker.options'])
            );
        };
    }
}
