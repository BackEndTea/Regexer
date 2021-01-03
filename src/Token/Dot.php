<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token;

use BackEndTea\Regexer\Token;

final class Dot extends Token
{
    public static function create(): self
    {
        return new self('.');
    }
}
