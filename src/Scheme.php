<?php

namespace Krixon\URL;

class Scheme
{
    use StringValued;
    
    
    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        if (!preg_match('/^[a-z]([a-z0-9\+\.-]+)?$/i', $value)) {
            throw new \InvalidArgumentException('Invalid scheme.');
        }
        
        $this->value = strtolower($value);
    }
    
    
    /**
     * @param string $scheme
     *
     * @return bool
     */
    public function is($scheme)
    {
        return $this->toString() === $scheme;
    }
    
    
    /**
     * @return bool
     */
    public function isHTTP()
    {
        return $this->is('http');
    }
    
    
    /**
     * @return bool
     */
    public function isHTTPS()
    {
        return $this->is('https');
    }
    
    
    /**
     * @return bool
     */
    public function isFTP()
    {
        return $this->is('ftp');
    }
}
