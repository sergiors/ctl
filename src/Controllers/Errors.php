<?php

declare(strict_types=1);

namespace Sergiors\Ctl\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Sergiors\Ctl\Logger\JsonLogging;

final class Errors
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        if (false === file_exists(getenv('LOGFILE'))) {
            return new JsonResponse([], 204);
        }

        $errors = (new JsonLogging(
            getenv('LOGFILE')
        ))->lines();

        return new JsonResponse($errors);
    }
}
