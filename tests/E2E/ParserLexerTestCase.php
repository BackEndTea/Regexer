<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E;

use BackEndTea\Regexer\E2E\Fixture\UnableToParseRegex;
use BackEndTea\Regexer\Lexer\Lexer;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Parser\TokenParser;
use BackEndTea\Regexer\StringStream;
use BackEndTea\Regexer\Token;
use BackEndTea\Regexer\Util\Util;
use Generator;
use PHPUnit\Framework\TestCase;
use Throwable;

use function preg_match;
use function sprintf;

abstract class ParserLexerTestCase extends TestCase
{
    /** @return Generator<array{0:string, 1:array<Token>, 2:Node, 3?: array<string>, 4?: array<string>}> */
    abstract public static function provideTestCases(): Generator;

    /**
     * @param array<Token>  $expectedTokens
     * @param array<string> $matches        Strings that match the input regex
     * @param array<string> $notMatches     Strings that do not match the input regex
     *
     * @dataProvider provideTestCases
     */
    public function testCanParseAndLexInput(
        string $input,
        array $expectedTokens,
        Node $expectedAst,
        array $matches = [],
        array $notMatches = [],
    ): void {
        // sanity check that this is a valid regex
        $result = preg_match($input, 'foo');

        self::assertNotFalse($result, sprintf(
            'Regex %s is not a valid regex',
            $input,
        ));

        foreach ($matches as $match) {
            $this->assertMatchesRegularExpression($input, $match);
        }

        foreach ($notMatches as $noMatch) {
            $this->assertDoesNotMatchRegularExpression($input, $noMatch);
        }

        $lexer  = new Lexer();
        $parser = new TokenParser($lexer);

        $result = $lexer->regexToTokenStream(new StringStream($input));

        $total = '';
        try {
            foreach ($result as $item) {
                $total .= $item->asString();
            }
        } catch (Throwable $t) {
            throw UnableToParseRegex::fromString($total, $t);
        }

        $realTokens = Util::iterableToList($lexer->regexToTokenStream(new StringStream($input)));

        $convertedAst = $parser->convert($realTokens);
        $this->assertSame($input, $convertedAst->asString(), 'Ast did not convert back to original regex.');

        $this->assertEquals($expectedTokens, $realTokens);

        $realAst = $parser->parse($input);
        $this->assertEquals($expectedAst, $realAst);

        $this->assertSame($input, $realAst->asString());
    }
}
