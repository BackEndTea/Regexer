<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

use function array_map;
use function implode;

final class BracketList extends NodeWithChildren
{
    use WithChildren;

    private bool $negated;

    /** @param array<Node> $children */
    public function __construct(
        bool $isNegated,
        array $children,
    ) {
        $this->negated  = $isNegated;
        $this->children = $children;
    }

    public function isNegated(): bool
    {
        return $this->negated;
    }

    public function setNegated(bool $negated): void
    {
        $this->negated = $negated;
    }

    public function asString(): string
    {
        return '[' .
            ($this->negated ? '^' : '') .
            implode(
                '',
                array_map(
                    static fn (Node $node): string => $node->asString(),
                    $this->children,
                ),
            )
            . ']';
    }
}
