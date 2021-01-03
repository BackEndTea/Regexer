<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token;

use BackEndTea\Regexer\Token;

final class LiteralCharacters extends Token
{
    public static function create(string $characters): LiteralCharacters
    {
        return new self($characters);
    }
}
