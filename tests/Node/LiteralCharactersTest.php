<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use PHPUnit\Framework\TestCase;

final class LiteralCharactersTest extends TestCase
{
    public function testCanBeManipulated(): void
    {
        $characters = new LiteralCharacters('abc');
        $this->assertSame('abc', $characters->getCharacters());
        $this->assertSame('abc', $characters->asString());

        $characters->setCharacters('def');
        $this->assertSame('def', $characters->getCharacters());
        $this->assertSame('def', $characters->asString());
    }
}
