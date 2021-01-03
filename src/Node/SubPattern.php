<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

use function array_map;
use function implode;

final class SubPattern extends NodeWithChildren
{
    use WithChildren;

    /**
     * @param array<Node> $children
     */
    public function __construct(array $children)
    {
        $this->children = $children;
    }

    public function asString(): string
    {
        return '(' .
            implode(
                '',
                array_map(
                    static fn (Node $node): string => $node->asString(),
                    $this->children
                )
            )
            . ')';
    }
}
