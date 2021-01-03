<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\BracketList;

use BackEndTea\Regexer\Token;

final class OneOf extends Token
{
    public static function create(string $characters): self
    {
        return new self($characters);
    }
}
