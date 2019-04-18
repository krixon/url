<?php

declare(strict_types=1);

namespace Krixon\URL;

use InvalidArgumentException;
use function sprintf;

class PortNumber
{
    private const MIN_PORT = 0;
    private const MAX_PORT = 65535;

    private $number;


    public function __construct(int $value)
    {
        if ($value < self::MIN_PORT || $value > self::MAX_PORT) {
            throw new InvalidArgumentException(sprintf(
                'Invalid port number %d. Port must be within range %d - %d.',
                $value,
                self::MIN_PORT,
                self::MAX_PORT
            ));
        }

        $this->number = $value;
    }


    public function __toString() : string
    {
        return $this->toString();
    }


    public function toString() : string
    {
        return (string) $this->toInt();
    }


    public function toInt() : int
    {
        return $this->number;
    }
}
