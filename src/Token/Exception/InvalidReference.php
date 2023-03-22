<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use function sprintf;

final class InvalidReference extends SyntaxException
{
    public static function fromKNotation(string $character): self
    {
        return new self(sprintf(
            'Only \',{ or < are allowed after \k, got "%s"',
            $character,
        ));
    }
}
