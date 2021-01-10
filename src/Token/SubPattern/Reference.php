<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\SubPattern;

use BackEndTea\Regexer\Token;

final class Reference extends Token
{
    public static function create(string $characters): self
    {
        return new self('\\' . $characters);
    }
}
