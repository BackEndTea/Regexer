<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use function sprintf;

final class InvalidModifiers extends SyntaxException
{
    public static function fromModifiers(string $modifiers): self
    {
        return new self(sprintf(
            'One of the modifiers is not allowed" %s"',
            $modifiers,
        ));
    }
}
