<?php

namespace Krixon\URL;

trait StringValued
{
    private $value;
    
    
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
        return $this->value;
    }
}
