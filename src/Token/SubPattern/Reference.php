<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\SubPattern;

use BackEndTea\Regexer\Token;

use function sprintf;

final class Reference extends Token
{
    public static function create(string $characters): self
    {
        return new self('\\' . $characters);
    }

    public static function forPNotation(string $name): self
    {
        return new self(sprintf(
            '(%s',
            $name
        ));
    }
}
