<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token;
use Generator;

final class NamedSubPatternTest extends ParserLexerTestCase
{
    public function provideTestCases(): Generator
    {
        yield 'Named sub pattern' => [
            "/(?'foo'ab)/",
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\SubPattern\Named::fromName("?'foo'"),
                Token\LiteralCharacters::create('ab'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [new Node\SubPattern([new Node\LiteralCharacters('ab')], true, new Node\SubPattern\Name("?'", 'foo'))], ''),
        ];

        yield 'Named sub pattern with <> notation' => [
            '/(?<foo>ab)/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\SubPattern\Named::fromName('?<foo>'),
                Token\LiteralCharacters::create('ab'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [new Node\SubPattern([new Node\LiteralCharacters('ab')], true, new Node\SubPattern\Name('?<', 'foo'))], ''),
        ];

        yield 'Named sub pattern with P notation' => [
            '/(?P<foo>ab)/',
            [
                Token\Delimiter::create('/'),
                Token\SubPattern\Start::create(),
                Token\SubPattern\Named::fromName('?P<foo>'),
                Token\LiteralCharacters::create('ab'),
                Token\SubPattern\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [new Node\SubPattern([new Node\LiteralCharacters('ab')], true, new Node\SubPattern\Name('?P<', 'foo'))], ''),
        ];
    }
}
