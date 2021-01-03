<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\BracketList;

use BackEndTea\Regexer\Token;

final class Start extends Token
{
    public static function create(): self
    {
        return new self('[');
    }
}
