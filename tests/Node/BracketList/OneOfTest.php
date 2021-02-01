<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node\BracketList;

use PHPUnit\Framework\TestCase;

final class OneOfTest extends TestCase
{
    public function testCanManipulate(): void
    {
        $oneOf = new OneOf('foo');
        $this->assertSame('foo', $oneOf->getCharacters());
        $this->assertSame('foo', $oneOf->asString());

        $oneOf->setCharacters('bar');

        $this->assertSame('bar', $oneOf->getCharacters());
        $this->assertSame('bar', $oneOf->asString());
    }
}
