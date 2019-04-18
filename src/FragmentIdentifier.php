<?php

declare(strict_types=1);

namespace Krixon\URL;

use InvalidArgumentException;
use function ltrim;
use function preg_match;

class FragmentIdentifier
{
    use StringValued;

    public function __construct(string $value)
    {
        $value = '#' . ltrim($value, '#');

        if (!preg_match('/^#[?%!$&\'()*+,;=a-zA-Z0-9-._~:@\/]*$/', $value)) {
            throw new InvalidArgumentException('Invalid fragment identifier');
        }

        $this->value = $value;
    }
}
