<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token;
use Generator;

final class SpecialDelimiterParserLexerTest extends ParserLexerTestCase
{
    public static function provideTestCases(): Generator
    {
        foreach (
            [
                ['{', '}'],
                ['(', ')'],
                ['<', '>'],
                ['[', ']'],
            ] as [$open, $close]
        ) {
            yield 'basic example delimiter ' . $open => [
                $open . '^foo.3' . $close,
                [
                    Token\Delimiter::create($open),
                    Token\Anchor\Start::create(),
                    Token\LiteralCharacters::create('foo'),
                    Token\Dot::create(),
                    Token\LiteralCharacters::create('3'),
                    Token\Delimiter::create($close),
                ],
                new Node\RootNode($open, [
                    new Node\Anchor\Start(),
                    new Node\LiteralCharacters('foo'),
                    new Node\Dot(),
                    new Node\LiteralCharacters('3'),
                ], ''),
                ['foob3'],
                ['(fooa3)'],
            ];

            yield 'delimiter with a capture group' . $open => [
                $open . '(foo){2,4}' . $close . 'i',
                [
                    Token\Delimiter::create($open),
                    Token\SubPattern\Start::create(),
                    Token\LiteralCharacters::create('foo'),
                    Token\SubPattern\End::create(),
                    Token\Quantifier\QuantifierToken::fromCharacters('{2,4}'),
                    Token\Delimiter::create($close),
                    Token\Modifier::fromModifiers('i'),
                ],
                new Node\RootNode($open, [
                    new Node\Quantified(
                        new Node\SubPattern([new Node\LiteralCharacters('foo')]),
                        2,
                        4,
                        false,
                    ),
                ], 'i'),
                ['fooFoo'],
            ];

            yield 'delimiter with multiple capture groups' . $open => [
                $open . '((foo)|(bar)){2,4}' . $close . 'i',
                [
                    Token\Delimiter::create($open),
                    Token\SubPattern\Start::create(),
                    Token\SubPattern\Start::create(),
                    Token\LiteralCharacters::create('foo'),
                    Token\SubPattern\End::create(),
                    Token\Or_::create(),
                    Token\SubPattern\Start::create(),
                    Token\LiteralCharacters::create('bar'),
                    Token\SubPattern\End::create(),
                    Token\SubPattern\End::create(),
                    Token\Quantifier\QuantifierToken::fromCharacters('{2,4}'),
                    Token\Delimiter::create($close),
                    Token\Modifier::fromModifiers('i'),
                ],
                new Node\RootNode($open, [
                    new Node\Quantified(
                        new Node\SubPattern([
                            new Node\Or_(
                                new Node\SubPattern([new Node\LiteralCharacters('foo')]),
                                new Node\SubPattern([new Node\LiteralCharacters('bar')]),
                            ),
                        ]),
                        2,
                        4,
                        false,
                    ),
                ], 'i'),
                ['fooFoo'],
            ];

            yield 'delimiter with a bracket list' . $open => [
                $open . '[a-z2-5]*' . $close . 'i',
                [
                    Token\Delimiter::create($open),
                    Token\BracketList\Start::create(),
                    Token\BracketList\Range::fromCharacters('a-z'),
                    Token\BracketList\Range::fromCharacters('2-5'),
                    Token\BracketList\End::create(),
                    Token\Quantifier\QuantifierToken::star(),
                    Token\Delimiter::create($close),
                    Token\Modifier::fromModifiers('i'),
                ],
                new Node\RootNode($open, [
                    new Node\Quantified(
                        new Node\BracketList(
                            false,
                            [
                                new Node\BracketList\Range('a', 'z'),
                                new Node\BracketList\Range('2', '5'),
                            ],
                        ),
                        0,
                        null,
                        false,
                    ),
                ], 'i'),
                ['fooFoo'],
            ];
        }
    }
}
