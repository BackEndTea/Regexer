<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token;

use BackEndTea\Regexer\Token\Exception\InvalidModifiers;
use Generator;
use PHPUnit\Framework\TestCase;

final class ModifierTest extends TestCase
{
    /**
     * @dataProvider provideInvalidModifierCases
     */
    public function testDoesNotAllowInvalidModifiers(string $invalidModifiers): void
    {
        $this->expectException(InvalidModifiers::class);
        Modifier::fromModifiers($invalidModifiers);
    }

    /**
     * @return Generator<array{string}>
     */
    public function provideInvalidModifierCases(): Generator
    {
        yield ['1234567'];
        yield ['i_'];
        yield['iu2'];
        yield ['I'];
    }

    /**
     * @dataProvider provideValidModifiers
     */
    public function testAllowsValidModifiers(string $modifiers): void
    {
        $modifier = Modifier::fromModifiers($modifiers);

        $this->assertSame($modifiers, $modifier->asString());
    }

    /**
     * @return Generator<array{string}>
     */
    public function provideValidModifiers(): Generator
    {
        yield [''];
        yield ['i'];
        yield['u'];
        yield ['iu'];
    }
}
