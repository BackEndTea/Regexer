<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Quantifier;

use BackEndTea\Regexer\Token;

final class Lazy extends Token
{
    public static function create(): self
    {
        return new self('?');
    }
}
