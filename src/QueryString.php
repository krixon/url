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
        $value = ltrim($this->toString(), '?');
        
        parse_str($value, $data);
        
        return $data;
    }
}
