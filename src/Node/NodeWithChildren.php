<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

abstract class NodeWithChildren extends Node
{
    /** @return Node[] */
    abstract public function getChildren(): array;

    /** @param Node[] $children */
    abstract public function setChildren(array $children): void;

    abstract public function addChild(Node $node): void;
}
