<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node\Anchor\End;
use BackEndTea\Regexer\Node\Anchor\Start;
use PHPUnit\Framework\TestCase;

final class NodeGroupTest extends TestCase
{
    public function testCanBeManipulated(): void
    {
        $group = new NodeGroup([
            new LiteralCharacters('abc'),
            new End(),
        ]);
        $this->assertSame('abc$', $group->asString());
        $this->assertInstanceOf(LiteralCharacters::class, $group->getChildren()[0]);
        $this->assertInstanceOf(End::class, $group->getChildren()[1]);

        $group->setChildren([
            new Start(),
            new LiteralCharacters('def'),
        ]);

        $this->assertSame('^def', $group->asString());
        $this->assertInstanceOf(Start::class, $group->getChildren()[0]);
        $this->assertInstanceOf(LiteralCharacters::class, $group->getChildren()[1]);
    }
}
