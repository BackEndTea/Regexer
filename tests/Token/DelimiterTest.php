<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DelimiterTest extends TestCase
{
    /**
     * @dataProvider provideInvalidDelimiter
     */
    public function testCantCreateInvalidDelimiter(string $invalidDelimiter): void
    {
        $this->expectException(InvalidArgumentException::class);
        Delimiter::create($invalidDelimiter);
    }

    /**
     * @return Generator<array{string}>
     */
    public function provideInvalidDelimiter(): Generator
    {
        yield ['\\'];
        yield ['a'];
        yield ["\n"];
        yield ['//'];
    }
}
