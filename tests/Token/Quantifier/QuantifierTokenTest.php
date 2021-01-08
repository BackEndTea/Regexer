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
        QuantifierToken::fromCharacters('foo');
    }

    public function testCantCreateQuantifierWithoutBrackets(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromCharacters('2,3');
    }

    public function testCantCreateFromNonNumericQuantifier(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromCharacters('{a,b}');
    }

    public function testCantCreateFromNonNumericForSingle(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromCharacters('{a}');
    }

    public function testCantCreateFromNonNumericForFirst(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromCharacters('{a,2}');
    }

    public function testCantCreateFromFloatQuantifier(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromCharacters('{2,2.7}');
    }

    public function testCantCreateFromMoreThanTwo(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromCharacters('{2,3,4}');
    }

    public function testCanBeOpenEnded(): void
    {
        $token = QuantifierToken::fromCharacters('{2,}');
        $this->assertSame('{2,}', $token->asString());
    }

    public function testMustBeClosed(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromCharacters('{1,2');
    }

    public function testMustBeOpened(): void
    {
        $this->expectException(InvalidQuantifier::class);
        QuantifierToken::fromCharacters('1,2}');
    }
}
