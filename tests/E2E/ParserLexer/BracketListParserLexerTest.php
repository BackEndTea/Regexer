<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token;
use Generator;

final class BracketListParserLexerTest extends ParserLexerTestCase
{
    public function provideTestCases(): Generator
    {
        yield 'simple bracketlist' => [
            '/[abc]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\OneOf::create('abc'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [new Node\BracketList(false, [new Node\BracketList\OneOf('abc')])],
                ''
            ),
        ];

        yield 'bracketlist with not' => [
            '/[^abc]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\Not::create(),
                Token\BracketList\OneOf::create('abc'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [new Node\BracketList(true, [new Node\BracketList\OneOf('abc')])],
                ''
            ),
        ];

        yield 'bracketlist with [' => [
            '/[^abc[]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\Not::create(),
                Token\BracketList\OneOf::create('abc['),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [new Node\BracketList(true, [new Node\BracketList\OneOf('abc[')])],
                ''
            ),
        ];

        yield 'bracketlist with ^' => [
            '/[^abc^d]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\Not::create(),
                Token\BracketList\OneOf::create('abc^d'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [new Node\BracketList(true, [new Node\BracketList\OneOf('abc^d')])],
                ''
            ),
        ];

        yield 'bracketlist with $' => [
            '/[abcd$]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\OneOf::create('abcd$'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [new Node\BracketList(false, [new Node\BracketList\OneOf('abcd$')])],
                ''
            ),
        ];

        yield 'bracketlist with range' => [
            '/[ba-z123]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\OneOf::create('b'),
                Token\BracketList\Range::fromCharacters('a-z'),
                Token\BracketList\OneOf::create('123'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\BracketList(false, [
                        new Node\BracketList\OneOf('b'),
                        new Node\BracketList\Range('a', 'z'),
                        new Node\BracketList\OneOf('123'),

                    ]),
                ],
                ''
            ),
        ];

        yield 'bracketlist with escaped range' => [
            '/[ba\-z123]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\OneOf::create('ba'),
                Token\Escaped\EscapedCharacter::fromCharacter('-'),
                Token\BracketList\OneOf::create('z123'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\BracketList(false, [
                        new Node\BracketList\OneOf('ba'),
                        new Node\Escaped('-'),
                        new Node\BracketList\OneOf('z123'),
                    ]),
                ],
                ''
            ),
        ];

        yield 'bracketlist with range where range is escaped' => [
            '/[b\]-\]123]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\OneOf::create('b'),
                Token\BracketList\Range::fromCharacters('\]-\]'),
                Token\BracketList\OneOf::create('123'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\BracketList(false, [
                        new Node\BracketList\OneOf('b'),
                        new Node\BracketList\Range('\]', '\]'),
                        new Node\BracketList\OneOf('123'),
                    ]),
                ],
                ''
            ),
        ];

        yield 'bracketlist with range where someting before is escaped' => [
            '/[\]ba-z123]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\Escaped\EscapedCharacter::fromCharacter(']'),
                Token\BracketList\OneOf::create('b'),
                Token\BracketList\Range::fromCharacters('a-z'),
                Token\BracketList\OneOf::create('123'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\BracketList(false, [
                        new Node\Escaped(']'),
                        new Node\BracketList\OneOf('b'),
                        new Node\BracketList\Range('a', 'z'),
                        new Node\BracketList\OneOf('123'),
                    ]),
                ],
                ''
            ),
        ];

        yield 'bracketlist that starts with -' => [
            '/[-123]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\OneOf::create('-123'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\BracketList(false, [
                        new Node\BracketList\OneOf('-123'),
                    ]),
                ],
                ''
            ),
        ];

        yield 'bracketlist with double ^ at start' => [
            '/[^^123]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\Not::create(),
                Token\BracketList\OneOf::create('^123'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\BracketList(true, [
                        new Node\BracketList\OneOf('^123'),
                    ]),
                ],
                ''
            ),
        ];

        yield 'bracketlist with double - at start' => [
            '/[--123]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\Range::fromCharacters('--1'),
                Token\BracketList\OneOf::create('23'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\BracketList(false, [
                        new Node\BracketList\Range('-', '1'),
                        new Node\BracketList\OneOf('23'),
                    ]),
                ],
                ''
            ),
        ];

        yield 'multiple bracket list' => [
            '/[foo][bar]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\OneOf::create('foo'),
                Token\BracketList\End::create(),
                Token\BracketList\Start::create(),
                Token\BracketList\OneOf::create('bar'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\BracketList(
                        false,
                        [new Node\BracketList\OneOf('foo')]
                    ),
                    new Node\BracketList(
                        false,
                        [new Node\BracketList\OneOf('bar')]
                    ),
                ],
                ''
            ),
        ];

        yield 'multiple bracket list, one negated' => [
            '/[^foo][bar]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\Not::create(),
                Token\BracketList\OneOf::create('foo'),
                Token\BracketList\End::create(),
                Token\BracketList\Start::create(),
                Token\BracketList\OneOf::create('bar'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\BracketList(
                        true,
                        [new Node\BracketList\OneOf('foo')]
                    ),
                    new Node\BracketList(
                        false,
                        [new Node\BracketList\OneOf('bar')]
                    ),
                ],
                ''
            ),
        ];

        yield 'multiple bracket list, one with a range' => [
            '/[^foo][1a-z2]/',
            [
                Token\Delimiter::create('/'),
                Token\BracketList\Start::create(),
                Token\BracketList\Not::create(),
                Token\BracketList\OneOf::create('foo'),
                Token\BracketList\End::create(),
                Token\BracketList\Start::create(),
                Token\BracketList\OneOf::create('1'),
                Token\BracketList\Range::fromCharacters('a-z'),
                Token\BracketList\OneOf::create('2'),
                Token\BracketList\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode(
                '/',
                [
                    new Node\BracketList(
                        true,
                        [new Node\BracketList\OneOf('foo')]
                    ),
                    new Node\BracketList(
                        false,
                        [
                            new Node\BracketList\OneOf('1'),
                            new Node\BracketList\Range('a', 'z'),
                            new Node\BracketList\OneOf('2'),
                        ]
                    ),
                ],
                ''
            ),
        ];
    }
}
