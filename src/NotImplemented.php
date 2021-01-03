<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

use RuntimeException;

use function get_class;
use function sprintf;

final class NotImplemented extends RuntimeException
{
    public static function fromToken(Token $token): self
    {
        return new self(
            sprintf('Token "%s" was not yet implemented', get_class($token))
        );
    }
}
