<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

interface Stream
{
    /** @phpstan-impure */
    public function current(): string;

    /** @phpstan-impure */
    public function currentIndex(): int;

    /** @phpstan-impure */
    public function next(): string|null;

    public function moveTo(int $index): void;

    /** @phpstan-impure */
    public function at(int $index): string|null;

    /** @phpstan-impure */
    public function indexOfNext(string $char, int $startFrom): int|null;

    /** @phpstan-impure */
    public function getBetween(int $start, int $end): string;

    /** @phpstan-impure */
    public function getUntilEnd(): string;

    public function length(): int;
}
