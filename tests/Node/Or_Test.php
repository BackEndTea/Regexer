<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class Or_Test extends TestCase
{
    public function testCanBeManipulated(): void
    {
        $left  = new LiteralCharacters('abc');
        $right = new LiteralCharacters('def');
        $or    = new Or_($left, $right);

        $this->assertSame($left, $or->getLeft());
        $this->assertSame($right, $or->getRight());
        $this->assertSame('abc|def', $or->asString());

        $newRight = new NoopNode();
        $or->setRight($newRight);
        $this->assertSame($newRight, $or->getRight());

        $newLeft = new Dot();
        $or->setLeft($newLeft);
        $this->assertSame($newLeft, $or->getLeft());

        $this->assertSame('.|', $or->asString());

        $or->addChild(new LiteralCharacters('foo'));
        $this->assertSame('.|foo', $or->asString());
        $groupRight = $or->getRight();
        $this->assertInstanceOf(NodeGroup::class, $groupRight);
        $this->assertCount(1, $groupRight->getChildren());
    }

    public function testMustSetTwoChildren(): void
    {
        $or = new Or_(
            new NoopNode(),
            new NoopNode()
        );

        $this->expectException(InvalidArgumentException::class);
        $or->setChildren([new NoopNode(), new NoopNode(), new NoopNode()]);
    }

    public function testCanSetChildren(): void
    {
        $or = new Or_(
            new NoopNode(),
            new NoopNode()
        );

        $or->setChildren([
            'one' => new LiteralCharacters('abc'),
            'two' => new LiteralCharacters('def'),
        ]);

        $this->assertSame('abc|def', $or->asString());
    }
}
