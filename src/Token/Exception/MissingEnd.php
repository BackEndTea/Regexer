<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use function sprintf;

final class MissingEnd extends SyntaxException
{
    public static function fromOpening(string $char): self
    {
        return new self(
            sprintf(
                'Opening %s does not have an ending',
                $char,
            ),
        );
    }

    public static function fromDelimiter(string $char): self
    {
        return new self(
            sprintf(
                'Delimiter %s does not have an ending',
                $char,
            ),
        );
    }
}
