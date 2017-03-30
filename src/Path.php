<?php

namespace Krixon\URL;

class Path
{
    use StringValued;
    
    
    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        if ($value !== parse_url($value, PHP_URL_PATH)) {
            throw new \InvalidArgumentException('Invalid URL path.');
        }
        
        $this->value = $value;
    }
    
    
    /**
     * Determines if the path contains a traversal.
     * 
     * This is true if either . or .. are contained in the path as a single node.
     * 
     * @return bool
     */
    public function containsTraversal()
    {
        return (bool)preg_match('#/\.\.?(/|$)#', $this->toString());
    }
}
