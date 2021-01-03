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
                Token\Quantifier\QuantifierToken::fromBracketNotation('{3,5}'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\Quantified(
                    new Node\LiteralCharacters('a'),
                    '{3,5}',
                    3,
                    5
                ),
            ], ''),
        ];

        yield 'Exact quantifier' => [
            '/a{3}/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('a'),
                Token\Quantifier\QuantifierToken::fromBracketNotation('{3}'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [new Node\Quantified(new Node\LiteralCharacters('a'), '{3}', 3, 3)], ''),
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
                        new Node\Quantified(new Node\Escaped('d'), '+', 1, null),
                        new Node\LiteralCharacters('}'),
                    ]),
                    new Node\SubPattern([
                        new Node\LiteralCharacters('{'),
                        new Node\Quantified(new Node\Escaped('d'), '+', 1, null),
                        new Node\LiteralCharacters(','),
                        new Node\Quantified(new Node\Escaped('d'), '*', 0, null),
                        new Node\LiteralCharacters('}'),
                    ])
                ),
            ], ''),
        ];
    }
}
