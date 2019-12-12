<?php

namespace Krixon\URL\Test\URL;

use Krixon\URL\URL;

class URLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $string
     *
     * @dataProvider validURLStringProvider
     */
    public function testCanCreateInstanceFromString($string)
    {
        try {
            $this->assertInstanceOf(URL::class, URL::fromString($string));
        } catch (\InvalidArgumentException $e) {
            $this->fail("Cannot create URL instance from '$string': " . $e->getMessage());
        }
    }


    public function validURLStringProvider()
    {
        return [
            ['http://www.example.com/some/path?foo=bar#baz'],
        ];
    }


    /**
     * @param string $url
     * @param string $expected
     *
     * @dataProvider subDomainProvider
     */
    public function testCanExtractSubDomain($url, $expected)
    {
        $url = URL::fromString($url);

        $this->assertSame($expected, $url->subDomain());
    }


    public function subDomainProvider()
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
            'IP V4 - no subdomain' => [
                'http://10.0.0.1',
                '',
            ],
            'IP V4 with path - no subdomain' => [
                'http://10.0.0.1/myapp/dashboard',
                '',
            ],
        ];
    }


    /**
     * @param $string
     *
     * @dataProvider validURLStringProvider
     */
    public function testCanConvertToString($string)
    {
        $url = URL::fromString($string);

        $this->assertSame($string, $url->toString());
    }


    public function testCanRemoveQueryString()
    {
        $url      = URL::fromString('http://foo.example.com:80?foo=bar&bar=baz#foo');
        $removed  = $url->withoutQueryString();
        $expected = 'http://foo.example.com:80#foo';

        $this->assertSame($expected, $removed->toString());

        // Removing again should have no effect.
        $removed = $removed->withoutQueryString();

        $this->assertSame($expected, $removed->toString());
    }


    public function testCanRemoveFragmentIdentifier()
    {
        $url      = URL::fromString('http://foo.example.com:80?foo=bar&bar=baz#foo');
        $removed  = $url->withoutFragmentIdentifier();
        $expected = 'http://foo.example.com:80?foo=bar&bar=baz';

        $this->assertSame($expected, $removed->toString());

        // Removing again should have no effect.
        $removed = $removed->withoutFragmentIdentifier();

        $this->assertSame($expected, $removed->toString());
    }


    public function testCanRemovePort()
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
