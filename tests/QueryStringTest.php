<?php

namespace Krixon\URL\Test\URL;

use Krixon\URL\QueryString;

class QueryStringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $string
     * 
     * @dataProvider validQueryStringProvider
     */
    public function testCanCreateInstance($string)
    {
        try {
            $this->assertInstanceOf(QueryString::class, new QueryString($string));
        } catch (\InvalidArgumentException $e) {
            $this->fail("Cannot create query string instance from '$string'.");
        }
    }
    
    
    public function validQueryStringProvider()
    {
        return [
            ['foo=bar'],
            ['?foo=bar'],
            ['?foo=bar&bar=baz'],
            ['?foo=bar&bar=ba?z'],  // Only the first question mark is significant, others are literals.
            ['?foo=b?ar&bar=baz'],  // Only the first question mark is significant, others are literals.
            ['?foo=b?ar&bar=ba?z'], // Only the first question mark is significant, others are literals.
        ];
    }


    /**
     * @param string $original
     * @param string $parameter
     * @param string $value
     * @param string $expected
     *
     * @dataProvider withAddedParameterProvider
     */
    public function testWithAddedParameter(string $original, string $parameter, string $value, string $expected)
    {
        $original = new QueryString($original);
        $new      = $original->withAddedParameter($parameter, $value);

        static::assertSame($expected, $new->toString());
    }


    public function withAddedParameterProvider()
    {
        return [
            ['?foo=bar', 'name', 'lister', '?foo=bar&name=lister'],
            ['?foo=bar', 'foo', 'bar', '?foo=bar&foo=bar'],
        ];
    }


    /**
     * @dataProvider canBeCastToArrayExpectationsProvider
     */
    public function testCanBeCastToArray(string $queryString, array $expected)
    {
        $queryString = new QueryString($queryString);

        static::assertSame($expected, $queryString->toArray());
    }


    public function canBeCastToArrayExpectationsProvider() : array
    {
        return [
            ['?foo=bar&bar=baz', ['foo' => 'bar', 'bar' => 'baz']],
            ['?foo.bar=baz&foo.baz=bar', ['foo.bar' => 'baz', 'foo.baz' => 'bar']],
            ['?foo%20bar=baz', ['foo bar' => 'baz']],
            ['?foo=bar&foo%20bar=foo%20bar%20baz', ['foo' => 'bar', 'foo bar' => 'foo bar baz']],
            ['?foo[]=1&foo[]=2&bar=2', ['foo' => ['1', '2'], 'bar' => '2']],
            ['?%C4%95=%C4%95&bar=2', ['ĕ' => 'ĕ', 'bar' => '2']],
        ];
    }
}
