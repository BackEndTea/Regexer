<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node\Anchor;

use BackEndTea\Regexer\Node;

final class Start extends Node
{
    public function asString(): string
    {
        return '^';
    }
}
