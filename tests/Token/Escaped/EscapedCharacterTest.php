<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Escaped;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EscapedCharacterTest extends TestCase
{
    /**
     * @dataProvider provideInvalidEscapedCharacters
     */
    public function testCanOnlyBeOneCharacter(string $invalidCharacter): void
    {
        $this->expectException(InvalidArgumentException::class);
        EscapedCharacter::fromCharacter($invalidCharacter);
    }

    /**
     * @return Generator<array{string}>
     */
    public function provideInvalidEscapedCharacters(): Generator
    {
        yield [''];
        yield ['ab'];
        yield ['asdfyo9a8yp'];
    }
}
