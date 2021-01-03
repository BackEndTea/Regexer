<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

use Stringable;

abstract class Node implements Stringable
{
    abstract public function asString(): string;

    final public function __toString(): string
    {
        return $this->asString();
    }
}
