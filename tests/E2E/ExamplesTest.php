<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E;

use Generator;
use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

use function preg_match;

/**
 * @coversNothing
 */
final class ExamplesTest extends TestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testExample(SplFileInfo $file): void
    {
        $p = new Process(
            [
                'php',
                $file->getRealPath(),
            ]
        );

        $p->mustRun();

        $this->assertTrue($p->isSuccessful(), 'Examples should run successfully');

        preg_match('/\$output = \'(.*)\';/', $file->getContents(), $matches);

        $this->assertNotSame([], $matches, 'Please add the expected output like so: "$output = \'/foo/\'"');

        $this->assertSame($matches[1], $p->getOutput());
    }

    /**
     * @return Generator<array{SplFileInfo}>
     */
    public function provideExamples(): Generator
    {
        /** @var Iterator<SplFileInfo> $files */
        $files = Finder::create()
            ->in(__DIR__ . '/../../Examples')
            ->name('*.php')
            ->files();

        foreach ($files as $file) {
            yield [$file];
        }
    }
}
