<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node\Anchor\End;
use PHPUnit\Framework\TestCase;

final class RootNodeTest extends TestCase
{
    public function testCanBeManipulated(): void
    {
        $root = new RootNode(
            '/',
            [new LiteralCharacters('foo')],
            'i'
        );

        $this->assertSame('/', $root->getDelimiter());
        $this->assertSame('i', $root->getModifiers());
        $this->assertCount(1, $root->getChildren());
        $this->assertSame('/foo/i', $root->asString());

        $root->setDelimiter('#');
        $root->addChild(new End());
        $root->setModifiers('iu');

        $this->assertSame('#', $root->getDelimiter());
        $this->assertSame('iu', $root->getModifiers());
        $this->assertCount(2, $root->getChildren());
        $this->assertSame('#foo$#iu', $root->asString());
    }
}
