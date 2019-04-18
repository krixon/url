<?php

declare(strict_types=1);

namespace Krixon\URL;

use InvalidArgumentException;
use function preg_match;
use function strtolower;

class Scheme
{
    use StringValued;

    public function __construct(string $value)
    {
        if (!preg_match('/^[a-z]([a-z0-9\+\.-]+)?$/i', $value)) {
            throw new InvalidArgumentException('Invalid scheme.');
        }

        $this->value = strtolower($value);
    }


    public function is(string $scheme) : bool
    {
        return $this->toString() === $scheme;
    }


    public function isHTTP() : bool
    {
        return $this->is('http');
    }


    public function isHTTPS() : bool
    {
        return $this->is('https');
    }


    public function isFTP() : bool
    {
        return $this->is('ftp');
    }
}
