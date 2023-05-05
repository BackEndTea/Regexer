<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Util;

use BackEndTea\Regexer\Token\Exception\InvalidQuantifier;

use function count;
use function explode;
use function is_numeric;
use function strlen;
use function strpos;
use function trim;

final class QuantifierValidator
{
    /** @return array{int, int|null} */
    public static function getMinAndMaxFromCharacters(string $quantifier): array
    {
        if (! self::isValidQuantifier($quantifier)) {
            throw InvalidQuantifier::fromCharacters($quantifier);
        }

        switch ($quantifier) {
            case '?':
                return [0, 1];

            case '+':
                return [1, null];

            case '*':
                return [0, null];

            default:
                $pattern = trim($quantifier, '{}');
                $items   = explode(',', $pattern);

                if (count($items) === 1) {
                    return [(int) $items[0], (int) $items[0]];
                }

                if ($items[1] === '') {
                    return [(int) $items[0], null];
                }

                return [(int) $items[0], (int) $items[1]];
        }
    }

    public static function isValidQuantifier(string $quantifier): bool
    {
        if ($quantifier === '?' || $quantifier === '*' || $quantifier === '+') {
            return true;
        }

        if (
            strpos($quantifier, '{') !== 0
            || strpos($quantifier, '}') !== strlen($quantifier) - 1
        ) {
            return false;
        }

        $pattern = trim($quantifier, '{}');

        $items = explode(',', $pattern);

        if (count($items) === 1) {
            return self::isIntegerish($items[0]);
        }

        return count($items) === 2
            && self::isIntegerish($items[0])
            && ($items[1] === '' || self::isIntegerish($items[1]));
    }

    private static function isIntegerish(string $number): bool
    {
        return is_numeric($number) && ((string) (int) $number) === $number;
    }
}
