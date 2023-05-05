<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token;

use BackEndTea\Regexer\Token\Exception\InvalidModifiers;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ModifierTest extends TestCase
{
    #[DataProvider('provideInvalidModifierCases')]
    public function testDoesNotAllowInvalidModifiers(string $invalidModifiers): void
    {
        $this->expectException(InvalidModifiers::class);
        Modifier::fromModifiers($invalidModifiers);
    }

    /** @return Generator<array{string}> */
    public static function provideInvalidModifierCases(): Generator
    {
        yield ['1234567'];
        yield ['i_'];
        yield['iu2'];
        yield ['I'];
        yield [''];
    }

    #[DataProvider('provideValidModifiers')]
    public function testAllowsValidModifiers(string $modifiers): void
    {
        $modifier = Modifier::fromModifiers($modifiers);

        $this->assertSame($modifiers, $modifier->asString());
    }

    /** @return Generator<array{string}> */
    public static function provideValidModifiers(): Generator
    {
        yield ['i'];
        yield['u'];
        yield ['iu'];
    }
}
