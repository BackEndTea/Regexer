<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token;
use Generator;

/**
 * Smoke testing some general PHPStan error message, to make sure we parse those
 */
final class PHPStanErrorMessageParserLexerTest extends ParserLexerTestCase
{
    public static function provideTestCases(): Generator
    {
        yield [
            '#^Function handleError\\(\\) has no return type specified\\.$#',
            [
                Token\Delimiter::create('#'),
                Token\Anchor\Start::create(),
                Token\LiteralCharacters::create('Function handleError'),
                Token\Escaped\EscapedCharacter::fromCharacter('('),
                Token\Escaped\EscapedCharacter::fromCharacter(')'),
                Token\LiteralCharacters::create(' has no return type specified'),
                Token\Escaped\EscapedCharacter::fromCharacter('.'),
                Token\Anchor\End::create(),
                Token\Delimiter::create('#'),
            ],
            new Node\RootNode('#', [
                new Node\Anchor\Start(),
                new Node\LiteralCharacters('Function handleError'),
                new Node\Escaped('('),
                new Node\Escaped(')'),
                new Node\LiteralCharacters(' has no return type specified'),
                new Node\Escaped('.'),
                new Node\Anchor\End(),
            ], ''),
            ['Function handleError() has no return type specified.'],
        ];

        yield [
            '#^Parameter \\#1 \\$path of function dirname expects string, string\\|false given\\.$#',
            [
                Token\Delimiter::create('#'),
                Token\Anchor\Start::create(),
                Token\LiteralCharacters::create('Parameter '),
                Token\Escaped\EscapedCharacter::fromCharacter('#'),
                Token\LiteralCharacters::create('1 '),
                Token\Escaped\EscapedCharacter::fromCharacter('$'),
                Token\LiteralCharacters::create('path of function dirname expects string, string'),
                Token\Escaped\EscapedCharacter::fromCharacter('|'),
                Token\LiteralCharacters::create('false given'),
                Token\Escaped\EscapedCharacter::fromCharacter('.'),
                Token\Anchor\End::create(),
                Token\Delimiter::create('#'),
            ],
            new Node\RootNode('#', [
                new Node\Anchor\Start(),
                new Node\LiteralCharacters('Parameter '),
                new Node\Escaped('#'),
                new Node\LiteralCharacters('1 '),
                new Node\Escaped('$'),
                new Node\LiteralCharacters('path of function dirname expects string, string'),
                new Node\Escaped('|'),
                new Node\LiteralCharacters('false given'),
                new Node\Escaped('.'),
                new Node\Anchor\End(),
            ], ''),
            ['Parameter #1 $path of function dirname expects string, string|false given.'],
            [
                'false',
                'false given.',
                'string',
            ],
        ];

        yield 'regex for phpstan ignore' => [
            '#^Parameter \\#1 \\$char of static method BackEndTea\\\\Regexer\\\\Token\\\\Exception\\\\MissingEnd\\:\\:fromDelimiter\\(\\) expects string, string\\|null given\\.$#',
            [
                Token\Delimiter::create('#'),
                Token\Anchor\Start::create(),
                Token\LiteralCharacters::create('Parameter '),
                Token\Escaped\EscapedCharacter::fromCharacter('#'),
                Token\LiteralCharacters::create('1 '),
                Token\Escaped\EscapedCharacter::fromCharacter('$'),
                Token\LiteralCharacters::create('char of static method BackEndTea'),
                Token\Escaped\EscapedCharacter::fromCharacter('\\'),
                Token\LiteralCharacters::create('Regexer'),
                Token\Escaped\EscapedCharacter::fromCharacter('\\'),
                Token\LiteralCharacters::create('Token'),
                Token\Escaped\EscapedCharacter::fromCharacter('\\'),
                Token\LiteralCharacters::create('Exception'),
                Token\Escaped\EscapedCharacter::fromCharacter('\\'),
                Token\LiteralCharacters::create('MissingEnd'),
                Token\Escaped\EscapedCharacter::fromCharacter(':'),
                Token\Escaped\EscapedCharacter::fromCharacter(':'),
                Token\LiteralCharacters::create('fromDelimiter'),
                Token\Escaped\EscapedCharacter::fromCharacter('('),
                Token\Escaped\EscapedCharacter::fromCharacter(')'),
                Token\LiteralCharacters::create(' expects string, string'),
                Token\Escaped\EscapedCharacter::fromCharacter('|'),
                Token\LiteralCharacters::create('null given'),
                Token\Escaped\EscapedCharacter::fromCharacter('.'),
                Token\Anchor\End::create(),
                Token\Delimiter::create('#'),
            ],
            new Node\RootNode('#', [
                new Node\Anchor\Start(),
                new Node\LiteralCharacters('Parameter '),
                new Node\Escaped('#'),
                new Node\LiteralCharacters('1 '),
                new Node\Escaped('$'),
                new Node\LiteralCharacters('char of static method BackEndTea'),
                new Node\Escaped('\\'),
                new Node\LiteralCharacters('Regexer'),
                new Node\Escaped('\\'),
                new Node\LiteralCharacters('Token'),
                new Node\Escaped('\\'),
                new Node\LiteralCharacters('Exception'),
                new Node\Escaped('\\'),
                new Node\LiteralCharacters('MissingEnd'),
                new Node\Escaped(':'),
                new Node\Escaped(':'),
                new Node\LiteralCharacters('fromDelimiter'),
                new Node\Escaped('('),
                new Node\Escaped(')'),
                new Node\LiteralCharacters(' expects string, string'),
                new Node\Escaped('|'),
                new Node\LiteralCharacters('null given'),
                new Node\Escaped('.'),
                new Node\Anchor\End(),
            ], ''),
        ];
    }
}
