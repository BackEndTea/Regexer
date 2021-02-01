<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use PHPUnit\Framework\TestCase;

final class EscapedTest extends TestCase
{
    public function testCanBeManipulated(): void
    {
        $escaped = new Escaped('$');
        $this->assertSame('$', $escaped->getEscapedCharacter());
        $this->assertSame('\$', $escaped->asString());

        $escaped->setEscapedCharacter('^');
        $this->assertSame('^', $escaped->getEscapedCharacter());
        $this->assertSame('\^', $escaped->asString());
    }
}
