<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Quantifier;

use BackEndTea\Regexer\Token;
use BackEndTea\Regexer\Token\Exception\InvalidQuantifier;

use function count;
use function explode;
use function is_numeric;
use function strlen;
use function strpos;
use function trim;

final class QuantifierToken extends Token
{
    private function __construct(string $content)
    {
        parent::__construct($content);
    }

    public static function plus(): self
    {
        return new self('+');
    }

    public static function star(): self
    {
        return new self('*');
    }

    public static function questionMark(): self
    {
        return new self('?');
    }

    public static function fromBracketNotation(string $characters): self
    {
        if (
            strpos($characters, '{') !== 0
            || strpos($characters, '}') !== strlen($characters) - 1
        ) {
            throw InvalidQuantifier::fromCharacters($characters);
        }

        $pattern = trim($characters, '{}');

        $items = explode(',', $pattern);

        if (count($items) === 1) {
            self::assertIntegerish($items[0]);

            return new self($characters);
        }

        if (count($items) === 2) {
            self::assertIntegerish($items[0]);
            if ($items[1] !== '') {
                self::assertIntegerish($items[1]);
            }

            return new self($characters);
        }

        throw InvalidQuantifier::fromCharacters($characters);
    }

    private static function assertIntegerish(string $number): void
    {
        if (! is_numeric($number) || ((string) (int) $number) !== $number) {
            throw InvalidQuantifier::fromCharacters($number);
        }
    }
}
