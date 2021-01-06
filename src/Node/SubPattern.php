<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

use function array_map;
use function implode;

final class SubPattern extends NodeWithChildren
{
    use WithChildren;

    private bool $isCapturing;

    /**
     * @param array<Node> $children
     */
    public function __construct(array $children, bool $isCapturing = true)
    {
        $this->children    = $children;
        $this->isCapturing = $isCapturing;
    }

    public function isCapturing(): bool
    {
        return $this->isCapturing;
    }

    public function setCapturing(bool $isCapturing): void
    {
        $this->isCapturing = $isCapturing;
    }

    public function asString(): string
    {
        return '(' .
            ($this->isCapturing ? '' : '?:') .
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
