<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token;
use Generator;

final class SimpleParserLexerTest extends ParserLexerTestCase
{
    public static function provideTestCases(): Generator
    {
        yield 'Only Delimiters' => [
            '//',
            [
                Token\Delimiter::create('/'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [], ''),
        ];

        yield 'Only delimiter not /' => [
            '**',
            [
                Token\Delimiter::create('*'),
                Token\Delimiter::create('*'),
            ],
            new Node\RootNode('*', [], ''),
        ];

        yield 'characters' => [
            '/abc/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('abc'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [new Node\LiteralCharacters('abc')], ''),
        ];

        yield 'characters or others' => [
            '/foo|bar/i',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('foo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bar'),
                Token\Delimiter::create('/'),
                Token\Modifier::fromModifiers('i'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\Or_(
                        new Node\LiteralCharacters('foo'),
                        new Node\LiteralCharacters('bar'),
                    ),
                ],
                'i',
            ),
        ];

        yield 'multiple or' => [
            '/abc|bcd|cde/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('abc'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bcd'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('cde'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\Or_(
                        new Node\LiteralCharacters('abc'),
                        new Node\Or_(
                            new Node\LiteralCharacters('bcd'),
                            new Node\LiteralCharacters('cde'),
                        ),
                    ),
                ],
                '',
            ),
        ];

        yield 'With an escaped character' => [
            '/ab\dcd/u',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('ab'),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\LiteralCharacters::create('cd'),
                Token\Delimiter::create('/'),
                Token\Modifier::fromModifiers('u'),
            ],
            new Node\RootNode('/', [
                new Node\LiteralCharacters('ab'),
                new Node\Escaped('d'),
                new Node\LiteralCharacters('cd'),
            ], 'u'),
        ];

        yield 'dot' => [
            '/fo.|bar/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('fo'),
                Token\Dot::create(),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bar'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\NodeGroup([
                        new Node\LiteralCharacters('fo'),
                        new Node\Dot(),
                    ]),
                    new Node\LiteralCharacters('bar'),
                ),
            ], ''),
        ];

        yield 'hat' => [
            '/^foobar/',
            [
                Token\Delimiter::create('/'),
                Token\Anchor\Start::create(),
                Token\LiteralCharacters::create('foobar'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Anchor\Start(),
                new Node\LiteralCharacters('foobar'),
            ], ''),
        ];

        yield 'dollar' => [
            '/foobar$/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('foobar'),
                Token\Anchor\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\LiteralCharacters('foobar'),
                new Node\Anchor\End(),
            ], ''),
        ];

        yield 'Question mark as delimiter' => [
            '?a+?',
            [
                Token\Delimiter::create('?'),
                Token\LiteralCharacters::create('a'),
                Token\Quantifier\QuantifierToken::plus(),
                Token\Delimiter::create('?'),
            ],
            new Node\RootNode('?', [
                new Node\Quantified(
                    new Node\LiteralCharacters('a'),
                    1,
                    null,
                    false,
                ),
            ], ''),
        ];

        yield 'Question mark as delimiter with brackets quantifier' => [
            '?a{2}?',
            [
                Token\Delimiter::create('?'),
                Token\LiteralCharacters::create('a'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2}'),
                Token\Delimiter::create('?'),
            ],
            new Node\RootNode('?', [
                new Node\Quantified(
                    new Node\LiteralCharacters('a'),
                    2,
                    2,
                    false,
                ),
            ], ''),
        ];
    }
}
