<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use BackEndTea\Regexer\RegexerException;
use InvalidArgumentException;

use function sprintf;

final class MissingEnd extends InvalidArgumentException implements RegexerException
{
    public static function fromOpening(string $char): self
    {
        return new self(
            sprintf(
                'Opening %s does not have an ending',
                $char,
            )
        );
    }

    public static function fromDelimiter(string $char): self
    {
        return new self(
            sprintf(
                'Delimiter %s does not have an ending',
                $char,
            )
        );
    }
}
