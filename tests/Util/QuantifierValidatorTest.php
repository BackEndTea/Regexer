<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Util;

use BackEndTea\Regexer\Token\Exception\InvalidQuantifier;
use Generator;
use PHPUnit\Framework\TestCase;

final class QuantifierValidatorTest extends TestCase
{
    public function testThrowsOnInvalid(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierValidator::getMinAndMaxFromCharacters('fo');
    }

    /** @dataProvider provideQuantifierCases */
    public function testCalculatesCorrectQuantification(string $input, int $min, int|null $max): void
    {
        $this->assertSame([$min, $max], QuantifierValidator::getMinAndMaxFromCharacters($input));
    }

    /** @return Generator<array{string, int, int|null}> */
    public static function provideQuantifierCases(): Generator
    {
        yield ['?', 0, 1];
        yield ['+', 1, null];
        yield ['*', 0, null];
        yield ['{2}', 2, 2];
        yield ['{4,}', 4, null];
        yield['{4,5}', 4, 5];
    }
}
