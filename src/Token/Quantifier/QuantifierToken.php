<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Quantifier;

use BackEndTea\Regexer\Token;
use BackEndTea\Regexer\Token\Exception\InvalidQuantifier;
use BackEndTea\Regexer\Util\QuantifierValidator;

final class QuantifierToken extends Token
{
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
        if (! QuantifierValidator::isValidQuantifier($characters)) {
            throw InvalidQuantifier::fromCharacters($characters);
        }

        return new self($characters);
    }
}
