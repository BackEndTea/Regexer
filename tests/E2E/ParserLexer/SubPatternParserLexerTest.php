<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token;
use Generator;

final class SubPatternParserLexerTest extends ParserLexerTestCase
{
    public static function provideTestCases(): Generator
    {
        yield 'sub pattern with or' => [
            '#(foo)|(bar)#',
            [
                Token\Delimiter::create('#'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('foo'),
                Token\SubPattern\End::create(),
                Token\Or_::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('bar'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('#'),
            ],
            new Node\RootNode(
                '#',
                [
                    new Node\Or_(
                        new Node\SubPattern(
                            [new Node\LiteralCharacters('foo')],
                        ),
                        new Node\SubPattern(
                            [new Node\LiteralCharacters('bar')],
                        ),
                    ),
                ],
                '',
            ),
        ];

        yield 'nested sub pattern with or' => [
            '#(f(oo)|(aa))|(bar)#',
            [
                Token\Delimiter::create('#'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('f'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('oo'),
                Token\SubPattern\End::create(),
                Token\Or_::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('aa'),
                Token\SubPattern\End::create(),
                Token\SubPattern\End::create(),
                Token\Or_::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('bar'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('#'),
            ],
            new Node\RootNode(
                '#',
                [
                    new Node\Or_(
                        new Node\SubPattern([
                            new Node\Or_(
                                new Node\NodeGroup([
                                    new Node\LiteralCharacters('f'),
                                    new Node\SubPattern([new Node\LiteralCharacters('oo')]),
                                ]),
                                new Node\SubPattern([new Node\LiteralCharacters('aa')]),
                            ),
                        ]),
                        new Node\SubPattern([new Node\LiteralCharacters('bar')]),
                    ),
                ],
                '',
            ),
        ];

        yield 'non capturing' => [
            '/(?:foo)/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\SubPattern\NonCapturing::create(),
                Token\LiteralCharacters::create('foo'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [new Node\SubPattern([new Node\LiteralCharacters('foo')], false)], ''),
        ];

        yield 'question mark delimiter' => [
            '?(\?:foo)?',
            [
                Token\Delimiter::create('?'),
                Token\SubPattern\Start::create(),
                Token\Escaped\EscapedCharacter::fromCharacter('?'),
                Token\LiteralCharacters::create(':foo'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('?'),
            ],
            new Node\RootNode('?', [
                new Node\SubPattern([
                    new Node\Escaped('?'),
                    new Node\LiteralCharacters(':foo'),
                ]),
            ], ''),
        ];

        yield 'sub pattern with a backreference to it' => [
            '/(foo|bar)=\1/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('foo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bar'),
                Token\SubPattern\End::create(),
                Token\LiteralCharacters::create('='),
                Token\SubPattern\Reference::create('1'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\SubPattern([
                    new Node\Or_(
                        new Node\LiteralCharacters('foo'),
                        new Node\LiteralCharacters('bar'),
                    ),
                ], true),
                new Node\LiteralCharacters('='),
                new Node\SubPattern\Reference('\\', '1'),
            ], ''),
            [
                'foo=foo',
                'bar=bar',
            ],
            ['foo=bar'],
        ];

        yield 'double digit sub pattern' => [
            '/(1)(2)(3)(4)(5)(6)(7)(8)(9)(0)=\10ab/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('1'),
                Token\SubPattern\End::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('2'),
                Token\SubPattern\End::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('3'),
                Token\SubPattern\End::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('4'),
                Token\SubPattern\End::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('5'),
                Token\SubPattern\End::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('6'),
                Token\SubPattern\End::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('7'),
                Token\SubPattern\End::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('8'),
                Token\SubPattern\End::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('9'),
                Token\SubPattern\End::create(),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('0'),
                Token\SubPattern\End::create(),
                Token\LiteralCharacters::create('='),
                Token\SubPattern\Reference::create('10'),
                Token\LiteralCharacters::create('ab'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [

                new Node\SubPattern([new Node\LiteralCharacters('1')]),
                new Node\SubPattern([new Node\LiteralCharacters('2')]),
                new Node\SubPattern([new Node\LiteralCharacters('3')]),
                new Node\SubPattern([new Node\LiteralCharacters('4')]),
                new Node\SubPattern([new Node\LiteralCharacters('5')]),
                new Node\SubPattern([new Node\LiteralCharacters('6')]),
                new Node\SubPattern([new Node\LiteralCharacters('7')]),
                new Node\SubPattern([new Node\LiteralCharacters('8')]),
                new Node\SubPattern([new Node\LiteralCharacters('9')]),
                new Node\SubPattern([new Node\LiteralCharacters('0')]),
                new Node\LiteralCharacters('='),
                new Node\SubPattern\Reference('\\', '10'),
                new Node\LiteralCharacters('ab'),
            ], ''),
            ['1234567890=0ab'],
            ['1234567890=10ab'],
        ];

        yield 'sub pattern with a backreference to it, with "g" notation' => [
            '/(foo|bar)=\g1/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('foo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bar'),
                Token\SubPattern\End::create(),
                Token\LiteralCharacters::create('='),
                Token\SubPattern\Reference::create('g1'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\SubPattern([
                    new Node\Or_(
                        new Node\LiteralCharacters('foo'),
                        new Node\LiteralCharacters('bar'),
                    ),
                ], true),
                new Node\LiteralCharacters('='),
                new Node\SubPattern\Reference('\g', '1'),
            ], ''),
            [
                'foo=foo',
                'bar=bar',
            ],
            ['foo=bar'],
        ];

        yield 'sub pattern with a backreference to it, with "g{}" notation' => [
            '/(foo|bar)=\g{1}/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('foo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bar'),
                Token\SubPattern\End::create(),
                Token\LiteralCharacters::create('='),
                Token\SubPattern\Reference::create('g{1}'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\SubPattern([
                    new Node\Or_(
                        new Node\LiteralCharacters('foo'),
                        new Node\LiteralCharacters('bar'),
                    ),
                ], true),
                new Node\LiteralCharacters('='),
                new Node\SubPattern\Reference('\g{', '1'),
            ], ''),
            [
                'foo=foo',
                'bar=bar',
            ],
            ['foo=bar'],
        ];

        yield 'sub pattern with a relative backreference to it, with "g{}" notation' => [
            '/(foo|bar)=\g{-1}/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('foo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bar'),
                Token\SubPattern\End::create(),
                Token\LiteralCharacters::create('='),
                Token\SubPattern\Reference::create('g{-1}'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\SubPattern([
                    new Node\Or_(
                        new Node\LiteralCharacters('foo'),
                        new Node\LiteralCharacters('bar'),
                    ),
                ], true),
                new Node\LiteralCharacters('='),
                new Node\SubPattern\Reference('\g{', '-1'),
            ], ''),
            [
                'foo=foo',
                'bar=bar',
            ],
            ['foo=bar'],
        ];

        yield 'relative sub pattern with a backreference to it, with "g" notation' => [
            '/(foo|bar)=\g-1/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('foo'),
                Token\Or_::create(),
                Token\LiteralCharacters::create('bar'),
                Token\SubPattern\End::create(),
                Token\LiteralCharacters::create('='),
                Token\SubPattern\Reference::create('g-1'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\SubPattern([
                    new Node\Or_(
                        new Node\LiteralCharacters('foo'),
                        new Node\LiteralCharacters('bar'),
                    ),
                ], true),
                new Node\LiteralCharacters('='),
                new Node\SubPattern\Reference('\g', '-1'),
            ], ''),
            [
                'foo=foo',
                'bar=bar',
            ],
            ['foo=bar'],
        ];

        yield '\-1 is not a relative back reference' => [
            '/(foo)=\-1/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\LiteralCharacters::create('foo'),
                Token\SubPattern\End::create(),
                Token\LiteralCharacters::create('='),
                Token\Escaped\EscapedCharacter::fromCharacter('-'),
                Token\LiteralCharacters::create('1'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [
                new Node\SubPattern([new Node\LiteralCharacters('foo')]),
                new Node\LiteralCharacters('='),
                new Node\Escaped('-'),
                new Node\LiteralCharacters('1'),
            ], ''),
            ['foo=-1'],
            ['foo=foo'],
        ];
    }
}
