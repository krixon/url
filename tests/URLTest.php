<?php

declare(strict_types=1);

namespace Krixon\URL\Test\URL;

use InvalidArgumentException;
use Krixon\URL\QueryString;
use Krixon\URL\URL;
use PHPUnit\Framework\TestCase;
use function sprintf;

class URLTest extends TestCase
{
    /**
     * @dataProvider validURLStringProvider
     */
    public function testCanCreateInstanceFromString(string $string) : void
    {
        try {
            $this->assertInstanceOf(URL::class, URL::fromString($string));
        } catch (InvalidArgumentException $e) {
            $this->fail(sprintf("Cannot create URL instance from '%s': ", $string) . $e->getMessage());
        }
    }


    /**
     * @return mixed[]
     */
    public function validURLStringProvider() : array
    {
        return [
            ['http://www.example.com/some/path?foo=bar#baz'],
        ];
    }


    /**
     * @dataProvider subDomainProvider
     */
    public function testCanExtractSubDomain(string $url, string $expected) : void
    {
        $url = URL::fromString($url);

        $this->assertSame($expected, $url->subDomain());
    }


    /**
     * @return mixed[]
     */
    public function subDomainProvider() : array
    {
        return [
            'One sub-domain' => [
                'http://www.example.com',
                'www',
            ],
            'Two sub-domains' => [
                'http://www.foo.example.com',
                'www.foo',
            ],
            'Three sub-domains' => [
                'http://www.foo.bar.example.com',
                'www.foo.bar',
            ],
            'Two part top level domain' => [
                'http://www.foo.bar.example.co.uk',
                'www.foo.bar',
            ],
            'Two part top level domain 2' => [
                'http://www.foo.bar.example.org.uk',
                'www.foo.bar',
            ],
            'Two part top level domain 3' => [
                'http://www.foo.bar.example.police.uk',
                'www.foo.bar',
            ],
            'No sub-domain single' => [
                'http://localhost',
                '',
            ],
            'No sub-domain double' => [
                'http://example.com',
                '',
            ],
        ];
    }


    /**
     * @dataProvider validURLStringProvider
     */
    public function testCanConvertToString(string $string) : void
    {
        $url = URL::fromString($string);

        $this->assertSame($string, $url->toString());
        $this->assertSame($string, (string) $url);
    }


    public function testCanRemoveQueryString() : void
    {
        $url      = URL::fromString('http://foo.example.com:80?foo=bar&bar=baz#foo');
        $removed  = $url->withoutQueryString();
        $expected = 'http://foo.example.com:80#foo';

        $this->assertSame($expected, $removed->toString());

        // Removing again should have no effect.
        $removed = $removed->withoutQueryString();

        $this->assertSame($expected, $removed->toString());
    }


    public function testCanRemoveFragmentIdentifier() : void
    {
        $url      = URL::fromString('http://foo.example.com:80?foo=bar&bar=baz#foo');
        $removed  = $url->withoutFragmentIdentifier();
        $expected = 'http://foo.example.com:80?foo=bar&bar=baz';

        $this->assertSame($expected, $removed->toString());

        // Removing again should have no effect.
        $removed = $removed->withoutFragmentIdentifier();

        $this->assertSame($expected, $removed->toString());
    }


    public function testCanRemovePort() : void
    {
        $url      = URL::fromString('http://foo.example.com:80?foo=bar&bar=baz#foo');
        $removed  = $url->withoutPort();
        $expected = 'http://foo.example.com?foo=bar&bar=baz#foo';

        $this->assertSame($expected, $removed->toString());

        // Removing again should have no effect.
        $removed = $removed->withoutPort();

        $this->assertSame($expected, $removed->toString());
    }


    /**
     * @dataProvider canDetermineIfEqualExpectationsProvider
     */
    public function testCanDetermineIfEqual(URL $url, URL $other, bool $expected) : void
    {
        $this->assertSame($url->equals($other), $expected);
    }


    /**
     * @return mixed[]
     */
    public function canDetermineIfEqualExpectationsProvider() : array
    {
        return [
            [
                URL::fromString('http://foo.example.com'),
                URL::fromString('http://foo.example.com'),
                true,
            ],
            [
                URL::fromString('https://foo.example.com'),
                URL::fromString('https://foo.example.com'),
                true,
            ],
            [
                URL::fromString('http://foo.example.com:8080'),
                URL::fromString('http://foo.example.com:8080'),
                true,
            ],
            [
                URL::fromString('http://foo.example.com/foo/bar'),
                URL::fromString('http://foo.example.com/foo/bar'),
                true,
            ],
            [
                URL::fromString('http://foo.example.com?foo=1&foo=2'),
                URL::fromString('http://foo.example.com?foo=1&foo=2'),
                true,
            ],
            [
                URL::fromString('http://foo.example.com#start'),
                URL::fromString('http://foo.example.com#start'),
                true,
            ],
            [
                URL::fromString('http://foo.example.com:8081/foo/bar?foo=1&bar=2#start'),
                URL::fromString('http://foo.example.com:8081/foo/bar?foo=1&bar=2#start'),
                true,
            ],
            [
                URL::fromString('https://foo.example.com'),
                URL::fromString('http://foo.example.com'),
                false,
            ],
            [
                URL::fromString('https://foo.example.com:8080'),
                URL::fromString('https://foo.example.com:8081'),
                false,
            ],
            [
                URL::fromString('https://foo.example.com/foo/bar'),
                URL::fromString('https://foo.example.com/foo/baz'),
                false,
            ],
            [
                URL::fromString('http://foo.example.com?foo=1&foo=2'),
                URL::fromString('http://foo.example.com?foo=1&foo=3'),
                false,
            ],
            [
                URL::fromString('http://foo.example.com#start'),
                URL::fromString('http://foo.example.com#end'),
                false,
            ],
        ];
    }

    public function testCanAddQueryString() : void
    {
        // assert that a new query string can be added.

        $url = URL::fromString('http://foo.example.com');

        $this->assertNull($url->queryString());

        $url = $url->withQueryString(new QueryString('foo=1&bar=2'));

        $this->assertSame($url->queryString()->toArray(), ['foo' => '1', 'bar' => '2']);

        // assert that an existing query string is replaced

        $url = $url->withQueryString(new QueryString('baz=1'));

        $this->assertSame($url->queryString()->toArray(), ['baz' => '1']);
    }


    public function testCanDetermineIfHasPath() : void
    {
        $urlWithPath    = URL::fromString('http://foo.example.com/foo/bar');
        $urlWithoutPath = URL::fromString('http://foo.example.com');

        $this->assertTrue($urlWithPath->hasPath());
        $this->assertFalse($urlWithoutPath->hasPath());
    }


    public function testReturnsExpectedPassword() : void
    {
        $urlWithPassword    = URL::fromString('http://foo:mypassword@foo.example.com');
        $urlWithoutPassword = URL::fromString('http://foo.example.com');

        $this->assertSame('mypassword', $urlWithPassword->password());
        $this->assertNull($urlWithoutPassword->password());
    }


    public function testCanDetermineIfHasPassword() : void
    {
        $urlWithPassword    = URL::fromString('http://foo:mypassword@foo.example.com');
        $urlWithoutPassword = URL::fromString('http://foo.example.com');

        $this->assertTrue($urlWithPassword->hasPassword());
        $this->assertFalse($urlWithoutPassword->hasPassword());
    }


    /**
     * @dataProvider canDetermineIfStartsWithExpectationsProvider
     */
    public function testCanDetermineIfStartsWith(URL $url, URL $other, bool $expected) : void
    {
        $this->assertSame($url->startsWith($other), $expected);
    }


    /**
     * @return mixed[]
     */
    public function canDetermineIfStartsWithExpectationsProvider() : array
    {
        return [
            [
                URL::fromString('http://foo.example.com/foo/bar/baz'),
                URL::fromString('http://foo.example.com'),
                true,
            ],
            [
                URL::fromString('http://foo.example.com/foo/bar/baz'),
                URL::fromString('http://bar.example.com'),
                false,
            ],
        ];
    }
}
