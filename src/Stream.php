<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

interface Stream
{
    public function current(): string;

    public function currentIndex(): int;

    public function next(): ?string;

    public function moveTo(int $index): void;

    public function at(int $index): ?string;

    public function indexOfNext(string $char, int $startFrom): ?int;

    public function getBetween(int $start, int $end): string;

    public function getUntilEnd(): string;
}
