<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node\SubPattern;

use BackEndTea\Regexer\Node;

final class Reference extends Node
{
    private string $referenceTo;

    public function __construct(string $referenceTo)
    {
        $this->referenceTo = $referenceTo;
    }

    public function asString(): string
    {
        return '\\' . $this->referenceTo;
    }
}
