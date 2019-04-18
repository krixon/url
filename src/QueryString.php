<?php

declare(strict_types=1);

namespace Krixon\URL;

use InvalidArgumentException;
use function array_combine;
use function array_keys;
use function array_map;
use function bin2hex;
use function http_build_query;
use function ltrim;
use function parse_str;
use function preg_match;
use function preg_replace_callback;
use function urldecode;

class QueryString
{
    use StringValued;

    public function __construct(string $value)
    {
        $value = '?' . ltrim($value, '?');

        if (!preg_match('/^\?([\w\.\-\[\]~&%+?]+(=([\w\.\-~&%+?]+)?)?)*$/', $value)) {
            throw new InvalidArgumentException('Invalid query string.');
        }

        $this->value = $value;
    }


    /**
     * @param mixed[] $parameters
     */
    public static function fromArray(array $parameters) : self
    {
        return new self('?' . http_build_query($parameters, '', '&'));
    }


    /**
     * @return string[]
     */
    public function toArray() : array
    {
        $parameters = [];

        $value = ltrim($this->toString(), '?');

        // Convert all the parameter keys into their respective hex values so that dots and spaces won't be converted
        // into underscores by the native parse_str function.
        $queryString = preg_replace_callback('/(?:^|(?<=&))[^=[]+/', static function ($match) {
            return bin2hex(urldecode($match[0]));
        }, $value);

        parse_str($queryString, $parameters);

        // Convert all the keys back to their original form.
        $keys = array_map('hex2bin', array_keys($parameters));

        return array_combine($keys, $parameters);
    }


    public function withAddedParameter(string $parameter, string $value) : self
    {
        $string  = $this->value;
        $string .= '&' . http_build_query([$parameter => $value], '', '&');

        return new self(ltrim($string, '&'));
    }
}
