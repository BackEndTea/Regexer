<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use function sprintf;

final class MissingStart extends SyntaxException
{
    public static function fromEnding(string $char): self
    {
        return new self(
            sprintf(
                'Ending %s does not have a start',
                $char,
            ),
        );
    }
}
