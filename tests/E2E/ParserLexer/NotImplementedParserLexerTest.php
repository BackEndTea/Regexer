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
            new Node\RootNode('/', [], ''),
        ];

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
            new Node\RootNode('/', [], ''),
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
            new Node\RootNode('/', [], ''),
        ];
    }
}
