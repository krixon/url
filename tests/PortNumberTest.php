<?php

declare(strict_types=1);

namespace Krixon\URL\Test\URL;

use InvalidArgumentException;
use Krixon\URL\PortNumber;
use PHPUnit\Framework\TestCase;
use function sprintf;

class PortNumberTest extends TestCase
{
    /**
     * @dataProvider validPortProvider
     */
    public function testCanBeInitiatedWithValidValue(int $value) : void
    {
        try {
            $this->assertInstanceOf(PortNumber::class, new PortNumber($value));
        } catch (InvalidArgumentException $e) {
            $this->fail(sprintf("Cannot create PortNumber instance with value '%d': ", $value) . $e->getMessage());
        }
    }


    /**
     * @return mixed[]
     */
    public function validPortProvider() : array
    {
        return [
            [8080],
            [8080],
            [0],
            [65535],
        ];
    }


    /**
     * @dataProvider invalidPortProvider
     */
    public function testRejectsInvalidValues(int $value) : void
    {
        $this->expectException(InvalidArgumentException::class);

        new PortNumber($value);
    }


    /**
     * @return mixed[]
     */
    public function invalidPortProvider() : array
    {
        return [
            [-1],
            [65536],
            [70000],
        ];
    }
}
