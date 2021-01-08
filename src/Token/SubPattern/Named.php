<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\SubPattern;

use BackEndTea\Regexer\Token;

final class Named extends Token
{
    public static function fromName(string $characters): self
    {
        return new self($characters);
    }
}
