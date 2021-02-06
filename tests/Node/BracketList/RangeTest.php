<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node\BracketList;

use PHPUnit\Framework\TestCase;

final class RangeTest extends TestCase
{
    public function testCanManipulate(): void
    {
        $range = new Range('a', 'z');

        $this->assertSame('a', $range->getFrom());
        $this->assertSame('z', $range->getTo());

        $this->assertSame('a-z', $range->asString());

        $range->setFrom('b');
        $range->setTo('y');

        $this->assertSame('b', $range->getFrom());
        $this->assertSame('y', $range->getTo());

        $this->assertSame('b-y', $range->asString());
    }
}
