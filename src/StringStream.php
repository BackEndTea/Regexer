<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

use function strlen;
use function substr;

final class StringStream implements Stream
{
    private string $input;
    private int $currentIndex;

    public function __construct(string $input)
    {
        $this->input        = $input;
        $this->currentIndex = 0;
    }

    public function current(): string
    {
        return $this->input[$this->currentIndex];
    }

    public function currentIndex(): int
    {
        return $this->currentIndex;
    }

    public function next(): ?string
    {
        ++$this->currentIndex;

        return $this->input[$this->currentIndex] ?? null;
    }

    public function moveTo(int $index): void
    {
        $this->currentIndex = $index;
    }

    public function at(int $index): ?string
    {
        return $this->input[$index] ?? null;
    }

    public function indexOfNext(string $char, int $startFrom): ?int
    {
        $max = strlen($this->input);
        for ($i = $startFrom; $i < $max; $i++) {
            if ($this->input[$i] === $char) {
                return $i;
            }
        }

        return null;
    }

    public function getBetween(int $start, int $end): string
    {
        return substr($this->input, $start, $end - $start + 1);
    }

    public function getUntilEnd(): string
    {
        return substr($this->input, $this->currentIndex);
    }
}
