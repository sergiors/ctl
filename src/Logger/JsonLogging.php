<?php

declare(strict_types=1);

namespace Sergiors\Ctl\Logger;

use Hoa\Stream\Context;
use Hoa\Stream\Stream;
use const Prelude\{isNull, values};
use function Prelude\{pipe, map, filter, not};

class JsonLogging extends Stream
{
    protected function &_open($streamName, Context $context = null)
    {
        $handle = null === $context
            ? fopen($streamName, 'r')
            : fopen($streamName, 'r', false, $context);

        return $handle;
    }

    protected function _close()
    {
        return fclose($this->getStream());
    }

    public function lines(): array
    {
        $gets = function ($handle): \Generator {
            fseek($handle, -51200, SEEK_END);

            while (false  === feof($handle)) {
                yield fgets($handle);
            }
        };

        $lines = function (\Iterator $iterator): array {
            $lines = [];

            foreach ($iterator as $line) {
                $lines[] = $line;
            }

            return $lines;
        };

        return pipe(
            $gets,
            $lines,
            map('json_decode'),
            filter(not(isNull)),
            values
        )($this->getStream());
    }
}
