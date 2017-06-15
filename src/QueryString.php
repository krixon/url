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
