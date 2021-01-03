<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use BackEndTea\Regexer\RegexerException;
use InvalidArgumentException;

use function sprintf;

final class InvalidDelimiter extends InvalidArgumentException implements RegexerException
{
    public static function fromDelimiter(string $delimiter): self
    {
        return new self(sprintf(
            '"%s" is not a valid delimiter',
            $delimiter
        ));
    }

    public static function insideOfBracketListAtPosition(int $position): self
    {
        return new self(sprintf(
            'Unescaped delimiter inside of a bracketlist, at postion %d is not allowed',
            $position
        ));
    }
}
