<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E;

use BackEndTea\Regexer\E2E\Fixture\RemoveQuantifierVisitor;
use BackEndTea\Regexer\Lexer\Lexer;
use BackEndTea\Regexer\Parser\TokenParser;
use BackEndTea\Regexer\Traverser;
use PHPUnit\Framework\TestCase;

final class SmokeTest extends TestCase
{
    public function testTraversal(): void
    {
        $parser = new TokenParser(new Lexer());
        $ast    = $parser->parse('/foo|bar{2,}/i');

        $traverser = new Traverser([new RemoveQuantifierVisitor()]);

        $traverser->traverse($ast);
        $this->assertSame('/foo|bar/i', $ast->asString());
    }
}
