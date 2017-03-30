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
     * @param $string
     *
     * @dataProvider validURLStringProvider
     */
    public function testCanConvertToString($string)
    {
        $url = URL::fromString($string);
        
        $this->assertSame($string, $url->toString());
    }
}
