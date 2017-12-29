<?php

declare(strict_types=1);

namespace Sergiors\Ctl\Pimple\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Docker\Docker;
use Docker\DockerClient as Client;

final class DockerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container[Docker::class] = function (Container $container) {
            $host = getenv('DOCKER_HOST') ?: 'unix:///var/run/docker.sock';
            
            return new Docker(
                new Client([
                    'remote_socket' => $host,
                ])
            );
        };
    }
}
