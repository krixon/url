<?php

declare(strict_types=1);

namespace Krixon\URL\Test\URL;

use InvalidArgumentException;
use Krixon\URL\QueryString;
use PHPUnit\Framework\TestCase;
use function sprintf;

class QueryStringTest extends TestCase
{
    /**
     * @dataProvider validQueryStringProvider
     */
    public function testCanCreateInstance(string $string) : void
    {
        try {
            $this->assertInstanceOf(QueryString::class, new QueryString($string));
        } catch (InvalidArgumentException $e) {
            $this->fail(sprintf("Cannot create query string instance from '%s'.", $string));
        }
    }


    /**
     * @return mixed[]
     */
    public function validQueryStringProvider() : array
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
     * @dataProvider withAddedParameterProvider
     */
    public function testWithAddedParameter(string $original, string $parameter, string $value, string $expected) : void
    {
        $original = new QueryString($original);
        $new      = $original->withAddedParameter($parameter, $value);

        static::assertSame($expected, $new->toString());
    }


    /**
     * @return mixed[]
     */
    public function withAddedParameterProvider() : array
    {
        return [
            ['?foo=bar', 'name', 'lister', '?foo=bar&name=lister'],
            ['?foo=bar', 'foo', 'bar', '?foo=bar&foo=bar'],
        ];
    }


    /**
     * @param string[] $expected
     *
     * @dataProvider canBeCastToArrayExpectationsProvider
     */
    public function testCanBeCastToArray(string $queryString, array $expected) : void
    {
        $queryString = new QueryString($queryString);

        static::assertSame($expected, $queryString->toArray());
    }


    /**
     * @return mixed[]
     */
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
