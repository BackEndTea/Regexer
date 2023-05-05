<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

trait WithChildren
{
    /** @var list<Node> */
    private array $children = [];

    /** @return list<Node> */
    public function getChildren(): array
    {
        return $this->children;
    }

    /** @param list<Node> $children */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function addChild(Node $node): void
    {
        $this->children[] = $node;
    }
}
