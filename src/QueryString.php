<?php

namespace Krixon\URL;

class QueryString
{
    use StringValued;
    
    
    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        $value = '?' . ltrim($value, '?');
        
        if (!preg_match('/^\?([\w\.\-\[\]~&%+?]+(=([\w\.\-~&%+?]+)?)?)*$/', $value)) {
            throw new \InvalidArgumentException('Invalid query string.');
        }
        
        $this->value = $value;
    }
    
    
    /**
     * @param array $parameters
     *
     * @return static
     */
    public static function fromArray(array $parameters)
    {
        return new static('?' . http_build_query($parameters, '', '&'));
    }
    
    
    /**
     * @return string[]
     */
    public function toArray()
    {
        $parameters = [];

        $value = ltrim($this->toString(), '?');

        // Convert all the parameter keys into their respective hex values so that dots and spaces won't be converted into
        // underscores by the native parse_str function.
        $queryString = preg_replace_callback('/(?:^|(?<=&))[^=[]+/', function ($match) {
            return bin2hex(urldecode($match[0]));
        }, $value);

        parse_str($queryString, $parameters);

        // Convert all the keys back from to their original form.
        $keys = array_map('hex2bin', array_keys($parameters));

        return array_combine($keys, $parameters);
    }


    /**
     * Returns a new instance which includes the specified parameter.
     *
     * @param string $parameter
     * @param string $value
     *
     * @return static
     */
    public function withAddedParameter(string $parameter, string $value)
    {
        $string  = $this->value;
        $string .= '&' . http_build_query([$parameter => $value], '', '&');

        return new static(ltrim($string, '&'));
    }
}
