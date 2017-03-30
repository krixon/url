<?php

namespace Krixon\URL;

class URL
{
    /**
     * @var Scheme
     */
    private $scheme;
    
    /**
     * @var string
     */
    private $host;
    
    /**
     * @var string|null
     */
    private $user;
    
    /**
     * @var string|null
     */
    private $password;
    
    /**
     * @var Path|null
     */
    private $path;
    
    /**
     * @var PortNumber|null
     */
    private $port;
    
    /**
     * @var QueryString|null
     */
    private $queryString;
    
    /**
     * @var FragmentIdentifier|null
     */
    private $fragmentIdentifier;
    
    /**
     * @var string
     */
    private $string;
    
    
    /**
     * @param Scheme                  $scheme
     * @param string                  $host
     * @param string|null             $user
     * @param string|null             $password
     * @param Path|null               $path
     * @param PortNumber|null         $port
     * @param QueryString|null        $queryString
     * @param FragmentIdentifier|null $fragmentIdentifier
     */
    public function __construct(
        Scheme $scheme,
        $host,
        $user = null,
        $password = null,
        Path $path = null,
        PortNumber $port = null,
        QueryString $queryString = null,
        FragmentIdentifier $fragmentIdentifier = null
    ) {
        $this->scheme             = $scheme;
        $this->host               = $host;
        $this->user               = $user;
        $this->password           = $password;
        $this->path               = $path;
        $this->port               = $port;
        $this->queryString        = $queryString;
        $this->fragmentIdentifier = $fragmentIdentifier;
    }
    
    
    /**
     * @param string $string
     *
     * @return static
     * @throws \InvalidArgumentException
     */
    public static function fromString($string)
    {
        try {
            
            if ($values = parse_url($string)) {
                
                $scheme   = isset($values['scheme'])   ? new Scheme($values['scheme'])               : null;
                $host     = isset($values['host'])     ? $values['host']                             : null;
                $user     = isset($values['user'])     ? $values['user']                             : null;
                $pass     = isset($values['pass'])     ? $values['pass']                             : null;
                $path     = isset($values['path'])     ? new Path($values['path'])                   : null;
                $port     = isset($values['port'])     ? new PortNumber($values['port'])             : null;
                $query    = isset($values['query'])    ? new QueryString($values['query'])           : null;
                $fragment = isset($values['fragment']) ? new FragmentIdentifier($values['fragment']) : null;
                
                return new static($scheme, $host, $user, $pass, $path, $port, $query, $fragment);
            }
            
        } catch (\Exception $e) {
            // Squash.
        }

        throw new \InvalidArgumentException('Invalid URL string.', 0, $e ?? null);
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
        if (null !== $this->string) {
            return $this->string;
        }
        
        $credentials = '';
        
        if ($this->hasUser()) {
            if ($this->hasPassword()) {
                $credentials = sprintf('%s:%s@', $this->user(), $this->password());
            } else {
                $credentials = $this->user() . '@';
            }
        }
        
        $port = '';
        
        if ($this->hasPort()) {
            $port = ':' . $this->port();
        }
    
        return $this->string = sprintf(
            '%s://%s%s%s%s%s%s',
            $this->scheme(),
            $credentials,
            $this->host(),
            $port,
            $this->path(),
            $this->queryString(),
            $this->fragmentIdentifier()
        );
    }
    
    
    /**
     * Determines if this URL starts with another URL.
     * 
     * For example "http://example.com/bar/baz" starts with "http", "http://example.", "http://example.com/bar" etc. 
     * 
     * @param URL $other
     *
     * @return bool
     */
    public function startsWith(URL $other)
    {
        return strcasecmp(substr($this, 0, strlen($other)), $other) === 0;
    }
    
    
    /**
     * @return Scheme
     */
    public function scheme()
    {
        return $this->scheme;
    }
    
    
    /**
     * @return string|null
     */
    public function user()
    {
        return $this->user;
    }
    
    
    /**
     * @return bool
     */
    public function hasUser()
    {
        return null !== $this->user();
    }
    
    
    /**
     * @return string|null
     */
    public function password()
    {
        return $this->password;
    }
    
    
    /**
     * @return bool
     */
    public function hasPassword()
    {
        return null !== $this->password();
    }
    
    
    /**
     * @return string
     */
    public function host()
    {
        return $this->host;
    }
    
    
    /**
     * @return Path|null
     */
    public function path()
    {
        return $this->path;
    }
    
    
    /**
     * @return bool
     */
    public function hasPath()
    {
        return null !== $this->path();
    }
    
    
    /**
     * @return bool
     */
    public function containsPathTraversal()
    {
        return $this->hasPath() && $this->path()->containsTraversal();
    }
    
    
    /**
     * @return PortNumber|null
     */
    public function port()
    {
        return $this->port;
    }
    
    
    /**
     * @return bool
     */
    public function hasPort()
    {
        return null !== $this->port();
    }
    
    
    /**
     * @return QueryString|null
     */
    public function queryString()
    {
        return $this->queryString;
    }
    
    
    /**
     * @param QueryString $queryString
     *
     * @return static
     */
    public function withQueryString(QueryString $queryString)
    {
        return new static(
            $this->scheme(),
            $this->host(),
            $this->user(),
            $this->password(),
            $this->path(),
            $this->port(),
            $queryString,
            $this->fragmentIdentifier()
        );
    }
    
    
    /**
     * @return FragmentIdentifier|string
     */
    public function fragmentIdentifier()
    {
        return $this->fragmentIdentifier;
    }
    
    
    /**
     * @param URL $other
     *
     * @return bool
     */
    public function equals(URL $other)
    {
        return (string)$this === (string)$other;
    }
}
