<?php

declare(strict_types=1);

namespace Krixon\URL;

use InvalidArgumentException;
use Throwable;
use const FILTER_VALIDATE_URL;
use function array_pop;
use function explode;
use function filter_var;
use function implode;
use function in_array;
use function is_array;
use function parse_url;
use function sprintf;
use function strcasecmp;
use function strlen;
use function substr;

class URL
{
    public const COUNTRY_SECOND_LEVEL_DOMAINS = [
        // Australia
        '.asn.au',
        '.com.au',
        '.net.au',
        '.id.au',
        '.org.au',
        '.edu.au',
        '.gov.au',
        '.csiro.au',
        '.act.au',
        '.nsw.au',
        '.nt.au',
        '.qld.au',
        '.sa.au',
        '.tas.au',
        '.vlc.au',
        '.wa.au',
        // UK
        '.co.uk',
        '.org.uk',
        '.me.uk',
        '.ltd.uk',
        '.plc.uk',
        '.net.uk',
        '.sch.uk',
        '.ac.uk',
        '.gov.uk',
        '.mod.uk',
        '.mil.uk',
        '.nhs.uk',
        '.police.uk',
        // USA
        '.al.us',
        '.ak.us',
        '.az.us',
        '.ar.us',
        '.ca.us',
        '.co.us',
        '.ct.us',
        '.de.us',
        '.dc.us',
        '.fl.us',
        '.ga.us',
        '.hi.us',
        '.id.us',
        '.il.us',
        '.in.us',
        '.ia.us',
        '.ks.us',
        '.ky.us',
        '.la.us',
        '.me.us',
        '.md.us',
        '.ma.us',
        '.mi.us',
        '.mn.us',
        '.ms.us',
        '.mo.us',
        '.mt.us',
        '.ne.us',
        '.nv.us',
        '.nh.us',
        '.nj.us',
        '.nm.us',
        '.ny.us',
        '.nc.us',
        '.nd.us',
        '.oh.us',
        '.ok.us',
        '.or.us',
        '.pa.us',
        '.ri.us',
        '.sc.us',
        '.sd.us',
        '.tn.us',
        '.tx.us',
        '.ut.us',
        '.vt.us',
        '.va.us',
        '.wa.us',
        '.wv.us',
        '.wi.us',
        '.wy.us',
        '.as.us',
        '.gu.us',
        '.mp.us',
        '.pr.us',
        '.vi.us',
        '.fed.us',
        '.isa.us',
        '.nsn.us',
        '.dni.us',
        '.kids.us',
    ];

    private $scheme;
    private $host;
    private $user;
    private $password;
    private $path;
    private $port;
    private $queryString;
    private $fragmentIdentifier;
    private $string;


    public function __construct(
        Scheme $scheme,
        string $host,
        ?string $user = null,
        ?string $password = null,
        ?Path $path = null,
        ?PortNumber $port = null,
        ?QueryString $queryString = null,
        ?FragmentIdentifier $fragmentIdentifier = null
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


    public static function fromString(string $string) : self
    {
        $isValid        = filter_var($string, FILTER_VALIDATE_URL);
        $invalidMessage =  'Invalid URL string.';

        if (!$isValid) {
            throw new InvalidArgumentException($invalidMessage);
        }

        $parts = parse_url($string);

        if (!is_array($parts)) {
            throw new InvalidArgumentException($invalidMessage);
        }

        try {
            return self::fromArray($parts);
        } catch (Throwable $e) {
            throw new InvalidArgumentException($invalidMessage, 0, $e);
        }
    }


    /**
     * @param mixed[] $values
     */
    public static function fromArray(array $values) : self
    {
        try {
            if (empty($values['scheme'])) {
                throw new InvalidArgumentException('No scheme provided for the URL.');
            }
            if (empty($values['host'])) {
                throw new InvalidArgumentException('No host provided for the URL.');
            }

            $scheme   = new Scheme($values['scheme']);
            $host     = $values['host'];
            $user     = $values['user'] ?? null;
            $pass     = $values['pass'] ?? null;
            $path     = isset($values['path']) ? new Path($values['path']) : null;
            $port     = isset($values['port']) ? new PortNumber($values['port']) : null;
            $query    = isset($values['query']) ? new QueryString($values['query']) : null;
            $fragment = isset($values['fragment']) ? new FragmentIdentifier($values['fragment']) : null;

            return new static($scheme, $host, $user, $pass, $path, $port, $query, $fragment);
        } catch (Throwable $e) {
            throw new InvalidArgumentException('Invalid URL array.', 0, $e);
        }
    }


    public function __toString() : string
    {
        return $this->toString();
    }


    public function toString() : string
    {
        if ($this->string !== null) {
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
     * For example "http://example.com/bar/baz" starts with "http", "http://example.com", "http://example.com/bar" etc.
     */
    public function startsWith(URL $other) : bool
    {
        $other = $other->toString();

        return strcasecmp(substr($this->toString(), 0, strlen($other)), $other) === 0;
    }


    public function scheme() : Scheme
    {
        return $this->scheme;
    }


    public function user() : ?string
    {
        return $this->user;
    }


    public function hasUser() : bool
    {
        return $this->user() !== null;
    }


    public function password() : ?string
    {
        return $this->password;
    }


    public function hasPassword() : bool
    {
        return $this->password() !== null;
    }


    public function host() : string
    {
        return $this->host;
    }


    /**
     * Makes an educated guess at the sub domain which appears below the hostname.
     *
     * For example, given the URL "http://www.example.com", this will return "www" and given
     * "http://foo.bar.example.com", this will return "foo.bar".
     *
     * There is some support for special second-level domain. For example, given "http://www.example.co.uk", this
     * will return "www", taking into account the ".co.uk" second-level. However this behaviour is not perfect; any
     * sub domain returned should be considered a best-guess only.
     */
    public function subDomain() : string
    {
        $parts  = explode('.', $this->host);
        $top    = array_pop($parts);
        $second = array_pop($parts);

        if ($second !== null) {
            $topLevel = sprintf('.%s.%s', $second, $top);

            if (strlen($second) === 2 || in_array($topLevel, self::COUNTRY_SECOND_LEVEL_DOMAINS, true)) {
                array_pop($parts);
            }
        }

        return implode('.', $parts);
    }


    public function path() : ?Path
    {
        return $this->path;
    }


    public function hasPath() : bool
    {
        return $this->path() !== null;
    }


    public function containsPathTraversal() : bool
    {
        return $this->hasPath() && $this->path()->containsTraversal();
    }


    public function port() : ?PortNumber
    {
        return $this->port;
    }


    public function hasPort() : bool
    {
        return $this->port() !== null;
    }


    public function withoutPort() : self
    {
        if (!$this->hasPort()) {
            return $this;
        }

        $instance = clone $this;

        $instance->port = null;

        return $instance;
    }


    public function queryString() : ?QueryString
    {
        return $this->queryString;
    }


    public function hasQueryString() : bool
    {
        return $this->queryString !== null;
    }


    public function withQueryString(QueryString $queryString) : self
    {
        $instance = clone $this;

        $instance->queryString = $queryString;

        return $instance;
    }


    public function withoutQueryString() : self
    {
        if (!$this->hasQueryString()) {
            return $this;
        }

        $instance = clone $this;

        $instance->queryString = null;

        return $instance;
    }


    public function fragmentIdentifier() : ?FragmentIdentifier
    {
        return $this->fragmentIdentifier;
    }


    public function hasFragmentIdentifier() : bool
    {
        return $this->fragmentIdentifier !== null;
    }


    public function withoutFragmentIdentifier() : self
    {
        if (!$this->hasFragmentIdentifier()) {
            return $this;
        }

        $instance = clone $this;

        $instance->fragmentIdentifier = null;

        return $instance;
    }


    public function equals(URL $other) : bool
    {
        return (string) $this === (string) $other;
    }
}
