<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node\Anchor\End;
use BackEndTea\Regexer\Node\SubPattern\Name;
use PHPUnit\Framework\TestCase;

final class SubPatternTest extends TestCase
{
    public function testCanBeManipulated(): void
    {
        $subPattern = new SubPattern(
            [new LiteralCharacters('abc')],
            true,
        );

        $this->assertCount(1, $subPattern->getChildren());
        $this->assertTrue($subPattern->isCapturing());
        $this->assertNull($subPattern->getName());
        $this->assertSame('(abc)', $subPattern->asString());

        $subPattern->addChild(new End());
        $subPattern->setCapturing(false);
        $this->assertSame('(?:abc$)', $subPattern->asString());
        $subPattern->setCapturing(true);
        $subPattern->setName(new Name('?<', 'ya'));
        $this->assertSame('(?<ya>abc$)', $subPattern->asString());
    }
}
