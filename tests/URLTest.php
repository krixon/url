<?php

declare(strict_types=1);

namespace Krixon\URL\Test\URL;

use InvalidArgumentException;
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
}
