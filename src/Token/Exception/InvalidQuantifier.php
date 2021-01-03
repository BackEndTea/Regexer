<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use function sprintf;

final class InvalidQuantifier extends SyntaxException
{
    public static function fromCharacters(string $characters): self
    {
        return new self(sprintf(
            'Unable to create quantifier tokens from following sequence: %s',
            $characters
        ));
    }
}
