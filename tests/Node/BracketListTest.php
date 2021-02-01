<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node\BracketList\OneOf;
use BackEndTea\Regexer\Node\BracketList\Range;
use PHPUnit\Framework\TestCase;

final class BracketListTest extends TestCase
{
    public function testCanBeManipulated(): void
    {
        $bracketList = new BracketList(false, [new OneOf('abc')]);
        $this->assertFalse($bracketList->isNegated());
        $this->assertInstanceOf(OneOf::class, $bracketList->getChildren()[0]);
        $this->assertCount(1, $bracketList->getChildren());
        $this->assertSame('[abc]', $bracketList->asString());

        $bracketList->setNegated(true);
        $bracketList->addChild(new Range('a', 'z'));
        $this->assertTrue($bracketList->isNegated());
        $this->assertCount(2, $bracketList->getChildren());
        $this->assertInstanceOf(Range::class, $bracketList->getChildren()[1]);
        $this->assertSame('[^abca-z]', $bracketList->asString());
    }
}
