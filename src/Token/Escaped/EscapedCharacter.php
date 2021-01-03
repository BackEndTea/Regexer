<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Escaped;

use BackEndTea\Regexer\Token;
use InvalidArgumentException;

use function strlen;

final class EscapedCharacter extends Token
{
    public static function fromCharacter(string $char): self
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('Can only escape one character');
        }

        return new self('\\' . $char);
    }
}
