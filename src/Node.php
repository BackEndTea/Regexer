<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

abstract class Node
{
    abstract public function asString(): string;
}
