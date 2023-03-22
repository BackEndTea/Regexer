<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node\SubPattern;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class NameTest extends TestCase
{
    /** @dataProvider provideInvalidCharacters */
    public function testCantBeCreatedFromInvalidCharacters(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        Name::fromCharacters($invalid);
    }

    /** @return Generator<array{string}> */
    public static function provideInvalidCharacters(): Generator
    {
        yield ['foo'];
        yield ['?:ab:'];
    }

    public function testMustHaveValidStart(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Named capture group must start with one of?\', ?<, ?P<');
        new Name('foo', 'bar');
    }

    public function testMustSetVAlidStart(): void
    {
        $name = new Name('?P<', 'bar');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Named capture group must start with one of?\', ?<, ?P<');

        $name->setStart('f');
    }

    public function testCanManipulate(): void
    {
        $name = new Name('?<', 'foo');

        $this->assertSame('foo', $name->getName());
        $this->assertSame('?<', $name->getStart());
        $this->assertSame('?<foo>', $name->asString());

        $name->setName('bar');
        $name->setStart('?P<');

        $this->assertSame('bar', $name->getName());
        $this->assertSame('?P<', $name->getStart());
        $this->assertSame('?P<bar>', $name->asString());
    }
}
