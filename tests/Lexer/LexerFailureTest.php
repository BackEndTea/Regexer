<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Lexer;

use BackEndTea\Regexer\StringStream;
use BackEndTea\Regexer\Token\Exception\EmptyRegex;
use BackEndTea\Regexer\Token\Exception\InvalidDelimiter;
use BackEndTea\Regexer\Token\Exception\InvalidReference;
use BackEndTea\Regexer\Token\Exception\InvalidSubPattern;
use BackEndTea\Regexer\Token\Exception\MissingEnd;
use BackEndTea\Regexer\Token\Exception\MissingStart;
use BackEndTea\Regexer\Token\Exception\UnclosedBracketList;
use BackEndTea\Regexer\Util\Util;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

use function preg_match;
use function sprintf;

final class LexerFailureTest extends TestCase
{
    /** @param class-string<Throwable> $expectedException */
    #[DataProvider('provideInvalidRegexes')]
    public function testItFailsOnParsing(string $input, string $expectedException): void
    {
        self::assertFalse(@preg_match($input, 'foo'), sprintf(
            'Expected regex "%s" to be an invalid regex',
            $input,
        ));
        $lexer = new Lexer();

        $this->expectException($expectedException);
        Util::iterableToList($lexer->regexToTokenStream(new StringStream($input)));
    }

    /** @return Generator<array{string, class-string<Throwable>}> */
    public static function provideInvalidRegexes(): Generator
    {
        yield 'no end tag' => [
            '/a(/',
            MissingEnd::class,
        ];

        yield 'delimiter inside of bracket list' => [
            '/[123/456]/',
            InvalidDelimiter::class,
        ];

        yield 'no ending delimiter' => [
            '/asdfasdf',
            MissingEnd::class,
        ];

        yield 'No ending delimiter with special delimiter' => [
            '(asdfasdf',
            MissingEnd::class,
        ];

        yield 'No ending delimiter with special delimiter that attempts to close with same' => [
            '(asdfasdf(',
            MissingEnd::class,
        ];

        yield 'closing subpattern without opening' => [
            '/foo)/',
            MissingStart::class,
        ];

        yield 'invalid delimiter "a"' => [
            'a9ujkla',
            InvalidDelimiter::class,
        ];

        yield ['/a[/', UnclosedBracketList::class];
        yield ['/[foo\]', UnclosedBracketList::class];
        yield ['/foo[ab\]cd', UnclosedBracketList::class];
        yield ['/foo[ab\]cd\\', UnclosedBracketList::class];

        yield ['/(?<bb)/', InvalidSubPattern::class];
        yield ['/(?\'bb>)/', InvalidSubPattern::class];
        yield ['/(?asdf)', InvalidSubPattern::class];
        yield ['/(?P\'ad\'bb)', InvalidSubPattern::class];

        yield ['/(?<3ab>dd)/', InvalidSubPattern::class];
        yield ['/(?F<ab>dd)/', InvalidSubPattern::class];

        yield 'cant end on backslash' => [
            '/foo\\',
            MissingEnd::class,
        ];

        yield 'invalid K reference' => [
            '/\kfoo/',
            InvalidReference::class,
        ];

        yield 'cant end on unescaped K' => [
            '/\k',
            MissingEnd::class,
        ];

        yield 'empty string' => [
            '',
            EmptyRegex::class,
        ];
    }
}
