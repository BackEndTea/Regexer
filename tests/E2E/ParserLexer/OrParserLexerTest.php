<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token;
use Generator;

final class OrParserLexerTest extends ParserLexerTestCase
{
    public static function provideTestCases(): Generator
    {
        yield 'multiple parts on left side' => [
            '/ab\d{2,3}\D|bar/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('ab'),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2,3}'),
                Token\Escaped\EscapedCharacter::fromCharacter('D'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bar'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\NodeGroup([
                        new Node\LiteralCharacters('ab'),
                        new Node\Quantified(
                            new Node\Escaped('d'),
                            2,
                            3,
                            false,
                        ),
                        new Node\Escaped('D'),
                    ]),
                    new Node\LiteralCharacters('bar'),
                ),
            ], ''),
        ];

        yield 'multiple parts on right side' => [
            '/bar|ab\d{2,3}\D/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('bar'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('ab'),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2,3}'),
                Token\Escaped\EscapedCharacter::fromCharacter('D'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('bar'),
                    new Node\NodeGroup([
                        new Node\LiteralCharacters('ab'),
                        new Node\Quantified(
                            new Node\Escaped('d'),
                            2,
                            3,
                            false,
                        ),
                        new Node\Escaped('D'),
                    ]),
                ),
            ], ''),
        ];

        yield 'Quantifier on right side of OR' => [
            '/foo|bar{2,}/i',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('foo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bar'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2,}'),
                Token\Delimiter::create('/'),
                Token\Modifier::fromModifiers('i'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('foo'),
                    new Node\NodeGroup([
                        new Node\LiteralCharacters('ba'),
                        new Node\Quantified(
                            new Node\LiteralCharacters('r'),
                            2,
                            null,
                            false,
                        ),
                    ]),
                ),
            ], 'i'),
        ];

        yield 'plus' => [
            '/fo|b+r/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('fo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('b'),
                Token\Quantifier\QuantifierToken::plus(),
                Token\LiteralCharacters::create('r'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('fo'),
                    new Node\NodeGroup([
                        new Node\Quantified(
                            new Node\LiteralCharacters('b'),
                            1,
                            null,
                            false,
                        ),
                        new Node\LiteralCharacters('r'),
                    ]),
                ),
            ], ''),
        ];

        yield 'multiple parts on left side, within a sub pattern' => [
            '/(ab\d{2,3}\D|bar)/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('ab'),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2,3}'),
                Token\Escaped\EscapedCharacter::fromCharacter('D'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bar'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\SubPattern([
                    new Node\Or_(
                        new Node\NodeGroup([
                            new Node\LiteralCharacters('ab'),
                            new Node\Quantified(
                                new Node\Escaped('d'),
                                2,
                                3,
                                false,
                            ),
                            new Node\Escaped('D'),
                        ]),
                        new Node\LiteralCharacters('bar'),
                    ),
                ]),
            ], ''),
        ];

        yield 'multiple parts on right side, within a sub pattern' => [
            '/(bar|ab\d{2,3}\D)/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('bar'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('ab'),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2,3}'),
                Token\Escaped\EscapedCharacter::fromCharacter('D'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\SubPattern([
                    new Node\Or_(
                        new Node\LiteralCharacters('bar'),
                        new Node\NodeGroup([
                            new Node\LiteralCharacters('ab'),
                            new Node\Quantified(
                                new Node\Escaped('d'),
                                2,
                                3,
                                false,
                            ),
                            new Node\Escaped('D'),
                        ]),
                    ),
                ]),
            ], ''),
        ];

        yield 'complexer Or with multiple sub patterns' => [
            '/((foo)[a-z]|(bar|baz))|(b{2,3}ar|c{2}\d)/iu',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('foo'),
                Token\SubPattern\End::create(),
                Token\BracketList\Start::create(),
                Token\BracketList\Range::fromCharacters('a-z'),
                Token\BracketList\End::create(),
                Token\Or_::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('bar'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('baz'),
                Token\SubPattern\End::create(),
                Token\SubPattern\End::create(),
                Token\Or_::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('b'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2,3}'),
                Token\LiteralCharacters::create('ar'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('c'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2}'),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('/'),
                Token\Modifier::fromModifiers('iu'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\SubPattern([
                        new Node\Or_(
                            new Node\NodeGroup([
                                new Node\SubPattern([
                                    new Node\LiteralCharacters('foo'),
                                ]),
                                new Node\BracketList(false, [new Node\BracketList\Range('a', 'z')]),
                            ]),
                            new Node\SubPattern([
                                new Node\Or_(
                                    new Node\LiteralCharacters('bar'),
                                    new Node\LiteralCharacters('baz'),
                                ),
                            ]),
                        ),
                    ]),
                    new Node\SubPattern([
                        new Node\Or_(
                            new Node\NodeGroup([
                                new Node\Quantified(
                                    new Node\LiteralCharacters('b'),
                                    2,
                                    3,
                                    false,
                                ),
                                new Node\LiteralCharacters('ar'),
                            ]),
                            new Node\NodeGroup([
                                new Node\Quantified(
                                    new Node\LiteralCharacters('c'),
                                    2,
                                    2,
                                    false,
                                ),
                                new Node\Escaped('d'),
                            ]),
                        ),
                    ]),
                ),
            ], 'iu'),
        ];

        yield 'questionmark' => [
            '/fo|b?r/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('fo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('b'),
                Token\Quantifier\QuantifierToken::questionMark(),
                Token\LiteralCharacters::create('r'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('fo'),
                    new Node\NodeGroup([
                        new Node\Quantified(
                            new Node\LiteralCharacters('b'),
                            0,
                            1,
                            false,
                        ),
                        new Node\LiteralCharacters('r'),
                    ]),
                ),
            ], ''),
        ];

        yield 'star' => [
            '/fo|b*r/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('fo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('b'),
                Token\Quantifier\QuantifierToken::star(),
                Token\LiteralCharacters::create('r'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('fo'),
                    new Node\NodeGroup([
                        new Node\Quantified(
                            new Node\LiteralCharacters('b'),
                            0,
                            null,
                            false,
                        ),
                        new Node\LiteralCharacters('r'),
                    ]),
                ),
            ], ''),
        ];

        yield 'escaped star' => [
            '/fo|b\*r/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('fo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('b'),
                Token\Escaped\EscapedCharacter::fromCharacter('*'),
                Token\LiteralCharacters::create('r'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('fo'),
                    new Node\NodeGroup([
                        new Node\LiteralCharacters('b'),
                        new Node\Escaped('*'),
                        new Node\LiteralCharacters('r'),
                    ]),
                ),
            ], ''),
        ];

        yield 'Quantify first on right side of or, not a literal character' => [
            '/foo|\d?ab/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('foo'),
                Token\Or_::create(),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\Quantifier\QuantifierToken::questionMark(),
                Token\LiteralCharacters::create('ab'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('foo'),
                    new Node\NodeGroup([
                        new Node\Quantified(
                            new Node\Escaped('d'),
                            0,
                            1,
                            false,
                        ),
                        new Node\LiteralCharacters('ab'),
                    ]),
                ),
            ], ''),
        ];

        yield 'Can quantify a bracket' => [
            '/{?/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('{'),
                Token\Quantifier\QuantifierToken::questionMark(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Quantified(
                    new Node\LiteralCharacters('{'),
                    0,
                    1,
                    false,
                ),
            ], ''),
        ];
    }
}
