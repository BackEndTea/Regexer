<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\Fixture;

use BackEndTea\Regexer\RegexerException;
use LogicException;
use Throwable;

use function sprintf;

final class UnableToParseRegex extends LogicException implements RegexerException
{
    public static function fromString(string $currentlyParsed, Throwable $previous): self
    {
        return new self(
            sprintf(
                'Error while parsing regex, currenlty parsed: "%s"',
                $currentlyParsed,
            ),
            previous: $previous,
        );
    }
}
