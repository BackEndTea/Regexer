<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token;

use BackEndTea\Regexer\Token;
use BackEndTea\Regexer\Token\Exception\InvalidDelimiter;

use function ctype_alnum;
use function ctype_print;
use function ctype_space;
use function strlen;

final class Delimiter extends Token
{
    public static function create(string $token): Delimiter
    {
        if (
            strlen($token) === 1
            && $token !== '\\'
            && ! ctype_alnum($token)
            && ! ctype_space($token)
            && ctype_print($token)
        ) {
            return new self($token);
        }

        throw InvalidDelimiter::fromDelimiter($token);
    }
}
