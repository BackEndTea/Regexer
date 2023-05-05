<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token;

use BackEndTea\Regexer\Token;
use BackEndTea\Regexer\Token\Exception\InvalidModifiers;

use function preg_match;

final class Modifier extends Token
{
    public static function fromModifiers(string $modifiers): self
    {
        self::assertValidModifier($modifiers);

        return new self($modifiers);
    }

    private static function assertValidModifier(string $modifiers): void
    {
        if ($modifiers === '' || preg_match('/[^imsxADSUXJu]/', $modifiers) === 1) {
            throw InvalidModifiers::fromModifiers($modifiers);
        }
    }
}
