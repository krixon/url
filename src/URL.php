<?php

namespace Krixon\URL;

class URL
{
    const COUNTRY_SECOND_LEVEL_DOMAINS = [
        // Australia
        '.asn.au', '.com.au', '.net.au', '.id.au', '.org.au', '.edu.au', '.gov.au', '.csiro.au', '.act.au', '.nsw.au',
        '.nt.au', '.qld.au', '.sa.au', '.tas.au', '.vlc.au', '.wa.au',
        // UK
        '.co.uk', '.org.uk', '.me.uk', '.ltd.uk', '.plc.uk', '.net.uk', '.sch.uk', '.ac.uk', '.gov.uk', '.mod.uk',
        '.mil.uk', '.nhs.uk', '.police.uk',
        // USA
        '.al.us', '.ak.us', '.az.us', '.ar.us', '.ca.us', '.co.us', '.ct.us', '.de.us', '.dc.us', '.fl.us', '.ga.us',
        '.hi.us', '.id.us', '.il.us', '.in.us', '.ia.us', '.ks.us', '.ky.us', '.la.us', '.me.us', '.md.us', '.ma.us',
        '.mi.us', '.mn.us', '.ms.us', '.mo.us', '.mt.us', '.ne.us', '.nv.us', '.nh.us', '.nj.us', '.nm.us', '.ny.us',
        '.nc.us', '.nd.us', '.oh.us', '.ok.us', '.or.us', '.pa.us', '.ri.us', '.sc.us', '.sd.us', '.tn.us', '.tx.us',
        '.ut.us', '.vt.us', '.va.us', '.wa.us', '.wv.us', '.wi.us', '.wy.us', '.as.us', '.gu.us', '.mp.us', '.pr.us',
        '.vi.us', '.fed.us', '.isa.us', '.nsn.us', '.dni.us', '.kids.us',
    ];

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


    public function __clone()
    {
        // Ensure the cached string representation is always cleared to avoid incorrect results when calling
        // toString() on the new instance.
        $this->string = null;
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
				return self::fromArray($values);
			}

		} catch (\Exception $e) {
			// Squash.
		}

		throw new \InvalidArgumentException('Invalid URL string.', 0, !empty($e) ? $e : null);

	}


	/**
	 * @param [] $array
	 *
	 * @return static
	 * @throws \InvalidArgumentException
	 */
	public static function fromArray($values)
	{
		try {

			$scheme   = isset($values['scheme'])   ? new Scheme($values['scheme'])               : null;
			$host     = isset($values['host'])     ? $values['host']                             : null;
			$user     = isset($values['user'])     ? $values['user']                             : null;
			$pass     = isset($values['pass'])     ? $values['pass']                             : null;
			$path     = isset($values['path'])     ? new Path($values['path'])                   : null;
			$port     = isset($values['port'])     ? new PortNumber($values['port'])             : null;
			$query    = isset($values['query'])    ? new QueryString($values['query'])           : null;
			$fragment = isset($values['fragment']) ? new FragmentIdentifier($values['fragment']) : null;

			return new static($scheme, $host, $user, $pass, $path, $port, $query, $fragment);

		} catch (\Exception $e) {
			// Squash.
		}

		throw new \InvalidArgumentException('Invalid URL array.', 0, !empty($e) ? $e : null);

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
     * Makes an educated guess at the subdomain which appears below the hostname.
     *
     * For example, given the URL "http://www.example.com", this will return "www" and given
     * "http://foo.bar.example.com", this will return "foo.bar".
     *
     * There is some support for special second-level domain. For example, given "http://www.example.co.uk", this
     * will return "www", taking into account the ".co.uk" second-level. However this behaviour is not perfect; any
     * subdomain returned should be considered a best-guess only.
     *
     * @return string
     */
    public function subDomain()
    {
        $parts = explode('.', $this->host);

        $top    = array_pop($parts);
        $second = array_pop($parts);

        if (strlen($second) === 2 || in_array("$second.$top", self::COUNTRY_SECOND_LEVEL_DOMAINS, true)) {
            array_pop($parts);
        }

        return implode('.', $parts);
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
     * @return static
     */
    public function withoutPort()
    {
        if (!$this->hasPort()) {
            return $this;
        }

        $instance = clone $this;

        $instance->port = null;

        return $instance;
    }
    
    
    /**
     * @return QueryString|null
     */
    public function queryString()
    {
        return $this->queryString;
    }


    /**
     * Determines if the URL contains a query string.
     *
     * @return bool
     */
    public function hasQueryString()
    {
        return null !== $this->queryString;
    }
    
    
    /**
     * Returns a version of this URL with the specified query string component.
     *
     * @param QueryString $queryString
     *
     * @return static
     */
    public function withQueryString(QueryString $queryString)
    {
        $instance = clone $this;

        $instance->queryString = $queryString;

        return $instance;
    }


    /**
     * Returns a version of this URL with no query string component.
     *
     * @return static
     */
    public function withoutQueryString()
    {
        if (!$this->hasQueryString()) {
            return $this;
        }

        $instance = clone $this;

        $instance->queryString = null;

        return $instance;
    }
    
    
    /**
     * @return FragmentIdentifier|string
     */
    public function fragmentIdentifier()
    {
        return $this->fragmentIdentifier;
    }


    /**
     * Determines if this URL includes a fragment identifier component.
     *
     * @return bool
     */
    public function hasFragmentIdentifier()
    {
        return null !== $this->fragmentIdentifier;
    }


    /**
     * Returns a version of this URL with no fragment identifier component.
     *
     * @return static
     */
    public function withoutFragmentIdentifier()
    {
        if (!$this->hasFragmentIdentifier()) {
            return $this;
        }

        $instance = clone $this;

        $instance->fragmentIdentifier = null;

        return $instance;
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
