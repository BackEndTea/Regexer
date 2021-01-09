<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

final class InvalidSubPattern extends SyntaxException
{
    public static function forIncompleteGroupStructure(): self
    {
        return new self('Got an invalid group structure');
    }

    public static function forInvalidCaptureGroupName(): self
    {
        return new self('Named capture group must be alpha numeric, and may not start with a digit');
    }
}
