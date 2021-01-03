<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use BackEndTea\Regexer\RegexerException;
use InvalidArgumentException;

use function sprintf;

final class InvalidQuantifier extends InvalidArgumentException implements RegexerException
{
    public static function fromCharacters(string $characters): self
    {
        return new self(sprintf(
            'Unable to create quantifier tokens from following sequence: %s',
            $characters
        ));
    }
}
