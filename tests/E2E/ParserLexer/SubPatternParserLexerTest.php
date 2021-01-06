<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token;
use Generator;

final class SubPatternParserLexerTest extends ParserLexerTestCase
{
    public function provideTestCases(): Generator
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
                            [new Node\LiteralCharacters('foo')]
                        ),
                        new Node\SubPattern(
                            [new Node\LiteralCharacters('bar')]
                        )
                    ),
                ],
                ''
            ),
        ];

        yield 'nestedsub pattern with or' => [
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
                                new Node\SubPattern([new Node\LiteralCharacters('aa')])
                            ),
                        ]),
                        new Node\SubPattern([new Node\LiteralCharacters('bar')])
                    ),
                ],
                ''
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
    }
}
