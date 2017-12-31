<?php

declare(strict_types=1);

namespace Sergiors\Ctl\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Docker\Docker;
use Docker\API\Model\ImageItem;
use DateTime;
use DateTimeImmutable;
use function array_map as map;

final class Images
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $images = $request
            ->getAttribute(ContainerInterface::class)
            ->get(Docker::class)
            ->getImageManager()
            ->findAll();

        return new JsonResponse(map(function (ImageItem $image) {
            $created = (new DateTimeImmutable)
                ->setTimestamp($image->getCreated());

            return [
                'id'           => $image->getId(),
                'tags'         => $image->getRepoTags() ?? [],
                'labels'       => $image->getLabels() ?? [],
                'size'         => $image->getSize(),
                'virtual_size' => $image->getVirtualSize(),
                'created'      => $created->format(DateTime::ATOM),
            ];
        }, $images));
    }
}
