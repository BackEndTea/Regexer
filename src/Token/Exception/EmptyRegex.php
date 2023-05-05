<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use BackEndTea\Regexer\RegexerException;

final class EmptyRegex extends SyntaxException implements RegexerException
{
    public static function create(): self
    {
        return new self('Unable to parse an empty regex.');
    }
}
