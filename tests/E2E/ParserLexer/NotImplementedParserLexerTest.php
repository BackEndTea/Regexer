<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\ParserLexer;

use BackEndTea\Regexer\E2E\ParserLexerTestCase;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\NotImplemented;
use BackEndTea\Regexer\Token;
use Generator;
use PHPUnit\Framework\AssertionFailedError;
use Throwable;

final class NotImplementedParserLexerTest extends ParserLexerTestCase
{
    /**
     * @param array<Token> $expectedTokens
     *
     * @dataProvider provideTestCases
     */
    public function testCanParseAndLexInput(string $input, array $expectedTokens = [], ?Node $expectedAst = null): void
    {
        if ($expectedTokens === [] || $expectedAst === null) {
            $this->markTestSkipped('Test incomplete');
        } else {
            try {
                parent::testCanParseAndLexInput($input, $expectedTokens, $expectedAst);
            } catch (AssertionFailedError | NotImplemented $e) {
                $this->markTestSkipped($e->getMessage());
            } catch (Throwable $t) {
                $this->markTestSkipped('Unexpected Error: ' . $t->getMessage());
            }

            $this->fail('Test is Passing, please move it to a correct test case');
        }
    }

    public function provideTestCases(): Generator
    {
        yield 'hat' => [
            '/^foobar/',
            [
                Token\Delimiter::create('/'),
                Token\Position\Start::create(),
                Token\LiteralCharacters::create('foobar'),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [], ''),
        ];

        yield 'dollar' => [
            '/foobar$/',
            [
                Token\Delimiter::create('/'),
                Token\LiteralCharacters::create('foobar'),
                Token\Position\End::create(),
                Token\Delimiter::create('/'),
            ],
            new Node\RootNode('/', [], ''),
        ];
    }
}
