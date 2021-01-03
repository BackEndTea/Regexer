<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use BackEndTea\Regexer\RegexerException;
use InvalidArgumentException;

use function sprintf;

final class MissingStart extends InvalidArgumentException implements RegexerException
{
    public static function fromEnding(string $char): self
    {
        return new self(
            sprintf(
                'Ending %s does not have a start',
                $char,
            )
        );
    }
}
