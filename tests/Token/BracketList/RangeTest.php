<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\BracketList;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RangeTest extends TestCase
{
    /** @dataProvider provideInvalidRangeCases */
    public function testCanNotBeCreatedFromInvalidCharacters(string $invalidRange): void
    {
        $this->expectException(InvalidArgumentException::class);
        Range::fromCharacters($invalidRange);
    }

    /** @return Generator<array{string}> */
    public static function provideInvalidRangeCases(): Generator
    {
        yield ['foo'];
        yield ['a'];
        yield ['ab-1'];
        yield ['q-f2'];
        yield ['\b-22'];
    }

    /** @dataProvider provideValidRangeCases */
    public function canBeRangeWithDash(string $inputRange): void
    {
        $this->assertSame($inputRange, Range::fromCharacters($inputRange)->asString());
    }

    /** @return Generator<array{string}> */
    public static function provideValidRangeCases(): Generator
    {
        yield ['\--\]'];
        yield ['\]-\-'];
        yield ['---'];
        yield ['--2'];
        yield ['a-z'];
    }
}
