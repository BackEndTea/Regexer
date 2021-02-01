<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class QuantifiedTest extends TestCase
{
    public function testCanBeManipulated(): void
    {
        $quantified = Quantified::fromString(
            new LiteralCharacters('abc'),
            '?'
        );
        $this->assertSame('abc?', $quantified->asString());
        $this->assertSame(0, $quantified->getMin());
        $this->assertSame(1, $quantified->getMax());
        $this->assertFalse($quantified->isLazy());

        $quantified->setLazy(true);
        $this->assertTrue($quantified->isLazy());

        $quantified->setMin(12);
        $quantified->setMax(13);
        $this->assertSame(12, $quantified->getMin());
        $this->assertSame(13, $quantified->getMax());

        $this->assertSame('abc{12,13}?', $quantified->asString());

        $quantified->setQuantifiedNode(new LiteralCharacters('def'));
        $this->assertSame('def{12,13}?', $quantified->asString());
        $quantified->setChildren([new Dot()]);
        $this->assertSame('.{12,13}?', $quantified->asString());
    }

    public function testCantAddChild(): void
    {
        $quantified = Quantified::fromString(
            new NoopNode(),
            '+'
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('cant add child to quantified node');
        $quantified->addChild(new NoopNode());
    }

    public function testCanSetOnlyOneChild(): void
    {
        $quantified = Quantified::fromString(
            new NoopNode(),
            '+'
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Can only quantify a single child');
        $quantified->setChildren([]);
    }
}
