<?php

declare(strict_types=1);

namespace Sergiors\Ctl\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Docker\Docker;
use Docker\API\Model\ContainerInfo;
use Docker\API\Model\Port;
use Docker\API\Model\Mount;
use DateTime;
use DateTimeImmutable;
use function array_map as map;

final class Containers
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $images = $request
            ->getAttribute(ContainerInterface::class)
            ->get(Docker::class)
            ->getContainerManager()
            ->findAll();

        return new JsonResponse(map(function (ContainerInfo $container) {
            $created = (new DateTimeImmutable)
                ->setTimestamp($container->getCreated());

            $mounts = map(function (Mount $mount) {
                return [
                    'name'        => $mount->getName() ?? '',
                    'source'      => $mount->getSource(),
                    'destination' => $mount->getDestination(),
                    'driver'      => $mount->getDriver() ?? '',
                    'mode'        => $mount->getMode(),
                    'rw'          => $mount->getRW(),
                    'propagation' => $mount->getPropagation(),
                ];
            }, $container->getMounts());

            $ports = map(function (Port $port) {
                return [
                    'private' => $port->getPrivatePort(),
                    'public'  => $port->getPublicPort() ?? '',
                    'type'    => $port->getType(),
                ];
            }, $container->getPorts());

            return [
                'id'      => $container->getId(),
                'image'   => $container->getImage(),
                'command' => $container->getCommand(),
                'status'  => $container->getStatus(),
                'ports'   => $ports,
                'mounts'  => $mounts,
                'labels'  => $container->getLabels(),
                'names'   => $container->getNames(),
                'created' => $created->format(DateTime::ATOM),
            ];
        }, $images));
    }
}
