<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token;

use BackEndTea\Regexer\Token;
use BackEndTea\Regexer\Token\Exception\InvalidDelimiter;

use function array_key_exists;
use function ctype_alnum;
use function ctype_print;
use function ctype_space;
use function strlen;

final class Delimiter extends Token
{
    private const SPECIAL_OPENING_TAGS_MAP = [
        '<' => '>',
        '[' => ']',
        '(' => ')',
        '{' => '}',
    ];

    public static function create(string $token): Delimiter
    {
        if (
            strlen($token) === 1
            && $token !== '\\'
            && ! ctype_alnum($token)
            && ! ctype_space($token)
            && ctype_print($token)
        ) {
            return new self($token);
        }

        throw InvalidDelimiter::fromDelimiter($token);
    }

    public static function isEndingFor(string $opening, string $closing): bool
    {
        if (array_key_exists($opening, self::SPECIAL_OPENING_TAGS_MAP)) {
            return self::SPECIAL_OPENING_TAGS_MAP[$opening] === $closing;
        }

        return $opening === $closing;
    }

    public static function getClosingDelimiter(string $opening): string
    {
        if (array_key_exists($opening, self::SPECIAL_OPENING_TAGS_MAP)) {
            return self::SPECIAL_OPENING_TAGS_MAP[$opening];
        }

        return $opening;
    }
}
