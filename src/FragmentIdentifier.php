<?php

namespace Krixon\URL;

class FragmentIdentifier
{
    use StringValued;
    
    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        $value = '#' . ltrim($value, '#');
        
        if (!preg_match('/^#[?%!$&\'()*+,;=a-zA-Z0-9-._~:@\/]*$/', $value)) {
            throw new \InvalidArgumentException('Invalid fragment identifier');
        }
        
        $this->value = $value;
    }
}
