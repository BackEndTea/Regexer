<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DelimiterTest extends TestCase
{
    /** @dataProvider provideInvalidDelimiter */
    public function testCantCreateInvalidDelimiter(string $invalidDelimiter): void
    {
        $this->expectException(InvalidArgumentException::class);
        Delimiter::create($invalidDelimiter);
    }

    /** @return Generator<array{string}> */
    public static function provideInvalidDelimiter(): Generator
    {
        yield ['\\'];
        yield ['a'];
        yield ["\n"];
        yield ['//'];
        yield [' '];
        yield ["\t"];
        yield ['5'];
    }

    /** @dataProvider provideValidDelimiter */
    public function testCanCreateValidDelimiter(string $delimiter): void
    {
        $this->assertMatchesRegularExpression($delimiter . 'foo' . $delimiter, 'foo', 'The delimiter was invalid or did somehow not match');

        $this->assertSame($delimiter, Delimiter::create($delimiter)->asString());
    }

    /** @return Generator<array{string}> */
    public static function provideValidDelimiter(): Generator
    {
        yield ['/'];
        yield ['#'];
        yield ['?'];
        yield ['$'];
        yield ['^'];
        yield ['_'];
        yield ['='];
        yield ['*'];
        yield ['+'];
        yield ['-'];
        yield [','];
        yield ['.'];
        yield ['.'];
        yield ['%'];
        yield ['@'];
        yield ['!'];
        yield [')'];
        yield ['}'];
        yield [']'];
    }
}
