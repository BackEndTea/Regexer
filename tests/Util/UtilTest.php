<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Util;

use Generator;
use PHPUnit\Framework\TestCase;

final class UtilTest extends TestCase
{
    public function testIterableToArray(): void
    {
        $in = [1, 2, 3];

        $this->assertSame($in, Util::iterableToArray($in));
        $this->assertSame($in, Util::iterableToArray($this->generate()));
    }

    /**
     * @return Generator<int>
     */
    private function generate(): Generator
    {
        yield 1;
        yield 2;
        yield 3;
    }
}
