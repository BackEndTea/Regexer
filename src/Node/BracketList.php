<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

use function array_map;
use function implode;

final class BracketList extends NodeWithChildren
{
    use WithChildren;

    private bool $isNegated;

    /**
     * @param array<Node> $children
     */
    public function __construct(
        bool $isNegated,
        array $children
    ) {
        $this->isNegated = $isNegated;
        $this->children  = $children;
    }

    public function isNegated(): bool
    {
        return $this->isNegated;
    }

    public function asString(): string
    {
        return '[' .
            ($this->isNegated ? '^' : '') .
            implode(
                '',
                array_map(
                    static fn (Node $node): string => $node->asString(),
                    $this->children
                )
            )
            . ']';
    }
}
