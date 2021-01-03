<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token;

use BackEndTea\Regexer\Token;

final class Or_ extends Token
{
    public static function create(): self
    {
        return new self('|');
    }
}
