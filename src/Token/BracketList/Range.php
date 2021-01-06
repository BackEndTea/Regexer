<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\BracketList;

use BackEndTea\Regexer\Token;
use InvalidArgumentException;

use function strlen;

final class Range extends Token
{
    public static function fromCharacters(string $characters): self
    {
        $max = strlen($characters);

        if (
            $max < 3
            || $max > 5
            || ($characters[0] !== '\\' && $characters[1] !== '-')
            || ($characters[$max - 2] !== '-' && $characters[$max - 2] !== '\\'
            )
        ) {
            throw new InvalidArgumentException('Range must be between two character');
        }

        return new self($characters);
    }
}
