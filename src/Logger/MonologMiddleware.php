<?php

declare(strict_types=1);

namespace Sergiors\Ctl\Logger;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;
use function Prelude\pipe;

final class MonologMiddleware implements MiddlewareInterface
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return pipe(
            [$delegate, 'process'],
            function (ResponseInterface $response) use ($request) {
                $this->logger->addRecord(
                    $this->translateLevel($response->getStatusCode()),
                    '',
                    [
                        'status_code' => $response->getStatusCode(),
                        'method'      => $request->getMethod(),
                        'uri'         => (string) $request->getUri(),
                        'headers'     => $request->getHeaders(),
                    ]
                );

                return $response;
            }
        )($request);
    }

    private function translateLevel(int $httpCode): int
    {
        switch ($httpCode) {
            case 200: // OK
            case 201: // Created
            case 202: // Accepted
            case 302: // Found
                return Logger::INFO;

            case 400: // Bad Request
            case 404: // Not Found
            case 406: // Not Acceptable
            case 502: // Bad Gateway
                return Logger::WARNING;

            case 500: //Internal Server Error
                return Logger::ERROR;
        }

        return Logger::DEBUG;
    }
}
