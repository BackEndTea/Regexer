<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token;
use Generator;

final class SimpleParserLexerTest extends ParserLexerTestCase
{
    public function provideTestCases(): Generator
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
                        new Node\LiteralCharacters('bar')
                    ),
                ],
                'i'
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
                        new Node\Or_(
                            new Node\LiteralCharacters('abc'),
                            new Node\LiteralCharacters('bcd'),
                        ),
                        new Node\LiteralCharacters('cde'),
                    ),
                ],
                ''
            ),
        ];
    }
}
