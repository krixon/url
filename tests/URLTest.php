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
            ['http://www.example.com', 'www'],
            ['http://www.foo.example.com', 'www.foo'],
            ['http://www.foo.bar.example.com', 'www.foo.bar'],
            ['http://localhost', ''],
            ['http://example.com', ''],
            ['http://*.com', ''],
            ['http://*.example.com', '*'],
            ['http://*.foo.example.com', '*.foo'],
            ['http://*.co.uk', ''],
            ['http://*.example.co.uk', '*'],
            ['http://*.foo.example.co.uk', '*.foo'],
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
