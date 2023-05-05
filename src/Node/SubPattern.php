<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

use function array_map;
use function implode;

final class SubPattern extends NodeWithChildren
{
    use WithChildren;

    /** @param list<Node> $children */
    public function __construct(
        array $children,
        private bool $isCapturing = true,
        private Node\SubPattern\Name|null $name = null,
    ) {
        $this->children = $children;
    }

    public function isCapturing(): bool
    {
        return $this->isCapturing;
    }

    public function setCapturing(bool $isCapturing): void
    {
        $this->isCapturing = $isCapturing;
    }

    public function getName(): SubPattern\Name|null
    {
        return $this->name;
    }

    public function setName(SubPattern\Name|null $name): void
    {
        $this->name = $name;
    }

    public function asString(): string
    {
        return '(' .
            ($this->isCapturing ? ($this->name ? $this->name->asString() : '') : '?:') .
            implode(
                '',
                array_map(
                    static fn (Node $node): string => $node->asString(),
                    $this->children,
                ),
            )
            . ')';
    }
}
