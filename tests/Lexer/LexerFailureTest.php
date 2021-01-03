<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Lexer;

use BackEndTea\Regexer\StringStream;
use BackEndTea\Regexer\Token\Exception\InvalidDelimiter;
use BackEndTea\Regexer\Token\Exception\MissingEnd;
use BackEndTea\Regexer\Token\Exception\MissingStart;
use BackEndTea\Regexer\Token\Exception\UnclosedBracketList;
use BackEndTea\Regexer\Util\Util;
use Generator;
use PHPStan\Testing\TestCase;

final class LexerFailureTest extends TestCase
{
    public function testFailsWithNoEndTag(): void
    {
        $lexer = new Lexer();

        $this->expectException(MissingEnd::class);
        Util::iterableToArray($lexer->regexToTokenStream(new StringStream('/a(/')));
    }

    public function testCantHaveDelimiterInsideOfBracketList(): void
    {
        $lexer = new Lexer();

        $this->expectException(InvalidDelimiter::class);
        Util::iterableToArray($lexer->regexToTokenStream(new StringStream('/[123/456]/')));
    }

    public function testNoEndingDelimiter(): void
    {
        $lexer = new Lexer();

        $this->expectException(MissingEnd::class);
        Util::iterableToArray($lexer->regexToTokenStream(new StringStream('/asdfasdf')));
    }

    public function testClosingSubPatternWithoutOpening(): void
    {
        $lexer = new Lexer();

        $this->expectException(MissingStart::class);
        Util::iterableToArray($lexer->regexToTokenStream(new StringStream('/foo)/')));
    }

    public function testInvalidDelimiter(): void
    {
        $lexer = new Lexer();

        $this->expectException(InvalidDelimiter::class);
        Util::iterableToArray($lexer->regexToTokenStream(new StringStream('a9ujkla')));
    }

    /**
     * @dataProvider provideMissingEndingTags
     */
    public function testEscapedBracketListEnding(string $regex): void
    {
        $lexer = new Lexer();

        $this->expectException(UnclosedBracketList::class);
        Util::iterableToArray($lexer->regexToTokenStream(new StringStream($regex)));
    }

    /**
     * @return Generator<array{string}>
     */
    public function provideMissingEndingTags(): Generator
    {
        yield ['/a[/'];
        yield ['/[foo\]'];
    }
}
