<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

use PHPUnit\Framework\TestCase;

final class StringStreamTest extends TestCase
{
    public function testCanFindNext(): void
    {
        $stream = new StringStream('foo{123}bar');

        $this->assertSame(7, $stream->indexOfNext('}', 3));
    }

    public function testCanGetSpecificIndex(): void
    {
        $stream = new StringStream('foo{123}bar');

        $this->assertSame('{', $stream->at(3));
    }

    public function testCanGetBetween(): void
    {
        $stream = new StringStream('foo{123}bar');

        $this->assertSame('{123}', $stream->getBetween(3, 7));
    }
}
