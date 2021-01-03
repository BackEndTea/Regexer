<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Quantifier;

use BackEndTea\Regexer\Token\Exception\InvalidQuantifier;
use PHPUnit\Framework\TestCase;

final class QuantifierTokenTest extends TestCase
{
    public function testCantCreateInvalidQuantifier(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromBracketNotation('foo');
    }

    public function testCantCreateFromNonNumericQuantifier(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromBracketNotation('{a,b}');
    }

    public function testCantCreateFromFloatQuantifier(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromBracketNotation('{2,2.7}');
    }

    public function testCantCreateFromMoreThanTwo(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromBracketNotation('{2,3,4}');
    }

    public function testCanBeOpenEnded(): void
    {
        $token = QuantifierToken::fromBracketNotation('{2,}');
        $this->assertSame('{2,}', $token->asString());
    }
}
