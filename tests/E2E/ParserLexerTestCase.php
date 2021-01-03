<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E;

use BackEndTea\Regexer\Lexer\Lexer;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Parser\TokenParser;
use BackEndTea\Regexer\StringStream;
use BackEndTea\Regexer\Token;
use BackEndTea\Regexer\Util\Util;
use Generator;
use PHPUnit\Framework\TestCase;

use function preg_match;

abstract class ParserLexerTestCase extends TestCase
{
    /**
     * @return Generator<array{string, array<Token>, Node}>
     */
    abstract public function provideTestCases(): Generator;

    /**
     * @param array<Token> $expectedTokens
     *
     * @dataProvider provideTestCases
     */
    public function testCanParseAndLexInput(string $input, array $expectedTokens, Node $expectedAst): void
    {
        // sanity check that this is a valid regex
        preg_match($input, 'foo');

        $lexer = new Lexer();

        $result = $lexer->regexToTokenStream(new StringStream($input));

        $this->assertEquals($expectedTokens, Util::iterableToArray($result));

        $parser  = new TokenParser($lexer);
        $realAst = $parser->parse($input);
        $this->assertEquals($expectedAst, $realAst);

        $this->assertSame($input, $realAst->asString(), 'Ast did not convert back to original regex.');
    }
}
