<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

final class NoopNode extends Node
{
    public function asString(): string
    {
        return '';
    }
}
