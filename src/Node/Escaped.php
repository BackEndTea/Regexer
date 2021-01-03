<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

final class Escaped extends Node
{
    private string $escapedCharacter;

    public function __construct(string $escapedCharacter)
    {
        $this->escapedCharacter = $escapedCharacter;
    }

    public function asString(): string
    {
        return '\\' . $this->escapedCharacter;
    }
}
