<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node\SubPattern;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ReferenceTest extends TestCase
{
    public function testCanBeManipulated(): void
    {
        $reference = new Reference('\k{', 'foo');
        $this->assertSame('\k{', $reference->getPrefix());
        $this->assertSame('foo', $reference->getReferenceTo());
        $this->assertSame('\k{foo}', $reference->asString());

        $reference->setPrefix('\g{');
        $reference->setReferenceTo('bar');

        $this->assertSame('\g{', $reference->getPrefix());
        $this->assertSame('bar', $reference->getReferenceTo());
        $this->assertSame('\g{bar}', $reference->asString());
    }

    public function testCantCreateWithInvalidPrefix(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Prefix must be one of: \, \g, \g{, \k{, \k<, \k');
        new Reference('f', 'bar');
    }

    public function testCantSetInvalidPrefix(): void
    {
        $r = new Reference('\g{', 'bar');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Prefix must be one of: \, \g, \g{, \k{, \k<, \k');

        $r->setPrefix('f');
    }
}
