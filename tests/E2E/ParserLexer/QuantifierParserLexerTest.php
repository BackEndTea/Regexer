<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token;
use Generator;

final class QuantifierParserLexerTest extends ParserLexerTestCase
{
    public function provideTestCases(): Generator
    {
        yield 'Quantifier with begin and end' => [
            '/a{3,5}/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('a'),
                Token\Quantifier\QuantifierToken::fromCharacters('{3,5}'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Quantified(
                    new Node\LiteralCharacters('a'),
                    3,
                    5,
                    false
                ),
            ], ''),
        ];

        yield 'Exact quantifier' => [
            '/a{3}/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('a'),
                Token\Quantifier\QuantifierToken::fromCharacters('{3}'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [new Node\Quantified(new Node\LiteralCharacters('a'), 3, 3, false)], ''),
        ];

        yield 'Escaped quantifier' => [
            '/abc{4,8\}/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('abc{4,8'),
                Token\Escaped\EscapedCharacter::fromCharacter('}'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\LiteralCharacters('abc{4,8'),
                new Node\Escaped('}'),
            ], ''),
        ];

        yield 'Quantifier that isn\'t closed' => [
            '/abc{4/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('abc{4'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [new Node\LiteralCharacters('abc{4')],
                ''
            ),
        ];

        yield 'Find quantifier regex' => [
            '/({\d+})|({\d+,\d*})/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('{'),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\Quantifier\QuantifierToken::plus(),
                Token\LiteralCharacters::create('}'),
                Token\SubPattern\End::create(),
                Token\Or_::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('{'),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\Quantifier\QuantifierToken::plus(),
                Token\LiteralCharacters::create(','),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\Quantifier\QuantifierToken::star(),
                Token\LiteralCharacters::create('}'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\SubPattern([
                        new Node\LiteralCharacters('{'),
                        new Node\Quantified(new Node\Escaped('d'), 1, null, false),
                        new Node\LiteralCharacters('}'),
                    ]),
                    new Node\SubPattern([
                        new Node\LiteralCharacters('{'),
                        new Node\Quantified(new Node\Escaped('d'), 1, null, false),
                        new Node\LiteralCharacters(','),
                        new Node\Quantified(new Node\Escaped('d'), 0, null, false),
                        new Node\LiteralCharacters('}'),
                    ])
                ),
            ], ''),
        ];

        yield 'Quantified of a character within other characters' => [
            '/ab{2}cd/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('ab'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2}'),
                Token\LiteralCharacters::create('cd'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\LiteralCharacters('a'),
                new Node\Quantified(
                    new Node\LiteralCharacters('b'),
                    2,
                    2,
                    false
                ),
                new Node\LiteralCharacters('cd'),
            ], ''),
        ];

        yield 'Quantified of a character within other characters on right side of OR' => [
            '/a|\dab{2}cd/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('a'),
                Token\Or_::create(),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\LiteralCharacters::create('ab'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2}'),
                Token\LiteralCharacters::create('cd'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('a'),
                    new Node\NodeGroup([
                        new Node\Escaped('d'),
                        new Node\LiteralCharacters('a'),
                        new Node\Quantified(
                            new Node\LiteralCharacters('b'),
                            2,
                            2,
                            false
                        ),
                        new Node\LiteralCharacters('cd'),
                    ])
                ),
            ], ''),
        ];

        yield 'Quantified of a character within other characters on right side of OR as the first token' => [
            '/a|ab{2}cd/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('a'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('ab'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2}'),
                Token\LiteralCharacters::create('cd'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('a'),
                    new Node\NodeGroup([
                        new Node\LiteralCharacters('a'),
                        new Node\Quantified(
                            new Node\LiteralCharacters('b'),
                            2,
                            2,
                            false
                        ),
                        new Node\LiteralCharacters('cd'),
                    ])
                ),
            ], ''),
        ];

        yield 'Quantified of a character within other characters on right side of OR as the first character' => [
            '/a|a{2}bcd/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('a'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('a'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2}'),
                Token\LiteralCharacters::create('bcd'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('a'),
                    new Node\NodeGroup([
                        new Node\Quantified(
                            new Node\LiteralCharacters('a'),
                            2,
                            2,
                            false
                        ),
                        new Node\LiteralCharacters('bcd'),
                    ])
                ),
            ], ''),
        ];

        yield 'lazy' => [
            '/foo??/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('foo'),
                Token\Quantifier\QuantifierToken::questionMark(),
                Token\Quantifier\Lazy::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\LiteralCharacters('fo'),
                new Node\Quantified(
                    new Node\LiteralCharacters('o'),
                    0,
                    1,
                    true
                ),
            ], ''),
        ];

        yield 'lazy quantifier inside of or' => [
            '/fo|\d{2}?\D/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('fo'),
                Token\Or_::create(),
                Token\Escaped\EscapedCharacter::fromCharacter('d'),
                Token\Quantifier\QuantifierToken::fromCharacters('{2}'),
                Token\Quantifier\Lazy::create(),
                Token\Escaped\EscapedCharacter::fromCharacter('D'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('fo'),
                    new Node\NodeGroup([
                        new Node\Quantified(
                            new Node\Escaped('d'),
                            2,
                            2,
                            true
                        ),
                        new Node\Escaped('D'),
                    ])
                ),
            ], ''),
        ];

        yield 'lazy quantifier inside of or for literal characters' => [
            '/fo|ab*?/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('fo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('ab'),
                Token\Quantifier\QuantifierToken::star(),
                Token\Quantifier\Lazy::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('fo'),
                    new Node\NodeGroup([
                        new Node\LiteralCharacters('a'),
                        new Node\Quantified(
                            new Node\LiteralCharacters('b'),
                            0,
                            null,
                            true
                        ),
                    ])
                ),
            ], ''),
        ];

        yield 'lazy quantifier inside of or for single literal character' => [
            '/fo|a*?/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('fo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('a'),
                Token\Quantifier\QuantifierToken::star(),
                Token\Quantifier\Lazy::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Or_(
                    new Node\LiteralCharacters('fo'),
                    new Node\Quantified(
                        new Node\LiteralCharacters('a'),
                        0,
                        null,
                        true
                    )
                ),
            ], ''),
        ];

        yield 'lazy quantifier  plus' => [
            '/a+?/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('a'),
                Token\Quantifier\QuantifierToken::plus(),
                Token\Quantifier\Lazy::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Quantified(
                    new Node\LiteralCharacters('a'),
                    1,
                    null,
                    true
                ),
            ], ''),
        ];
    }
}
