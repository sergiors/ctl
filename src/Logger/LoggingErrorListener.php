<?php

declare(strict_types=1);

namespace Sergiors\Yard\Logger;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LoggingErrorListener
{
    const LOG_FORMAT = '%d [%s] %s';

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(
        \Throwable $error,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->logger->error(
            sprintf(
                self::LOG_FORMAT,
                $response->getStatusCode(),
                $request->getMethod(),
                (string) $request->getUri()
            ),
            $error->getTrace()
        );
    }
}
