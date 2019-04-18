<?php

declare(strict_types=1);

namespace Krixon\URL;

use InvalidArgumentException;
use const PHP_URL_PATH;
use function parse_url;
use function preg_match;

class Path
{
    use StringValued;

    public function __construct(string $value)
    {
        if ($value !== parse_url($value, PHP_URL_PATH)) {
            throw new InvalidArgumentException('Invalid URL path.');
        }

        $this->value = $value;
    }


    /**
     * Determines if the path contains a traversal.
     *
     * This is true if either . or .. are contained in the path as a single node.
     */
    public function containsTraversal() : bool
    {
        return (bool) preg_match('#/\.\.?(/|$)#', $this->toString());
    }
}
