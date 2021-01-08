<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node\SubPattern;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class NameTest extends TestCase
{
    /**
     * @dataProvider provideInvalidCharacters
     */
    public function testCantBeCreatedFromInvalidCharacters(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        Name::fromCharacters($invalid);
    }

    /**
     * @return Generator<array{string}>
     */
    public function provideInvalidCharacters(): Generator
    {
        yield ['foo'];
        yield ['?:ab:'];
    }

    public function testMustHaveValidStart(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Name('foo', 'bar');
    }
}
