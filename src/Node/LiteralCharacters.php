<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

final class LiteralCharacters extends Node
{
    private string $characters;

    public function __construct(string $characters)
    {
        $this->characters = $characters;
    }

    public function getCharacters(): string
    {
        return $this->characters;
    }

    public function setCharacters(string $characters): void
    {
        $this->characters = $characters;
    }

    public function asString(): string
    {
        return $this->getCharacters();
    }
}
