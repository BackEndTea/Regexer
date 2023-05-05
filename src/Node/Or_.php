<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;
use InvalidArgumentException;

use function count;

final class Or_ extends NodeWithChildren
{
    public function __construct(private Node $left, private Node $right)
    {
    }

    public function getLeft(): Node
    {
        return $this->left;
    }

    public function setLeft(Node $left): void
    {
        $this->left = $left;
    }

    public function getRight(): Node
    {
        return $this->right;
    }

    public function setRight(Node $right): void
    {
        $this->right = $right;
    }

    /** @inheritDoc */
    public function getChildren(): array
    {
        return [$this->left, $this->right];
    }

    /** @inheritDoc */
    public function setChildren(array $children): void
    {
        if (count($children) !== 2) {
            throw new InvalidArgumentException('Or can only have 2 children');
        }

        $this->left  = $children[0];
        $this->right = $children[1];
    }

    public function addChild(Node $node): void
    {
        if (! $this->right instanceof Node\NodeGroup) {
            $this->right = $this->right instanceof Node\NoopNode
                ? new Node\NodeGroup([])
                : new Node\NodeGroup([$this->right]);
        }

        $this->right->addChild($node);
    }

    public function asString(): string
    {
        return $this->left->asString() . '|' . $this->right->asString();
    }
}
