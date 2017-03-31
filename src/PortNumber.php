<?php

namespace Krixon\URL;

class PortNumber
{
    private $number;
    
    
    /**
     * @param int $value
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        $options = [
            'options' => [
                'min_range' => 0,
                'max_range' => 65535,
            ],
        ];
        
        if (!filter_var($value, FILTER_VALIDATE_INT, $options)) {
            throw new \InvalidArgumentException('Invalid port number.');
        }

        $this->number = $value;
    }
    
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
    
    
    /**
     * @return string
     */
    public function toString()
    {
        return (string)$this->toInt();
    }
    
    
    /**
     * @return int
     */
    public function toInt()
    {
        return $this->number;
    }
}
