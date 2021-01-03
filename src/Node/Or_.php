<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;
use InvalidArgumentException;
use LogicException;

use function array_values;
use function count;

final class Or_ extends NodeWithChildren
{
    private Node $left;
    private Node $right;

    public function __construct(Node $left, Node $right)
    {
        $this->left  = $left;
        $this->right = $right;
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

    /**
     * @inheritDoc
     */
    public function getChildren(): array
    {
        return [$this->left, $this->right];
    }

    /**
     * @inheritDoc
     */
    public function setChildren(array $children): void
    {
        if (count($children) !== 2) {
            throw new LogicException('Or can only have 2 children');
        }

        $children = array_values($children);

        $this->left  = $children[0];
        $this->right = $children[1];
    }

    public function addChild(Node $node): void
    {
        throw new InvalidArgumentException('Cant add a child to an or, replace them instead');
    }

    public function asString(): string
    {
        return $this->left->asString() . '|' . $this->right->asString();
    }
}
