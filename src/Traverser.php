<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

use BackEndTea\Regexer\Node\NodeWithChildren;

final class Traverser
{
    /** @param NodeVisitor[] $visitors */
    public function __construct(private array $visitors)
    {
    }

    public function addVisitor(NodeVisitor $visitor): void
    {
        $this->visitors[] = $visitor;
    }

    public function traverse(Node $node): Node
    {
        foreach ($this->visitors as $visitor) {
            $visitor->before($node);
        }

        foreach ($this->visitors as $visitor) {
            $result = $visitor->enterNode($node);
            if ($result !== null) {
                $node = $result;
            }

            if ($node instanceof NodeWithChildren) {
                $node->setChildren($this->visitChildren($node->getChildren()));
            }

            $result = $visitor->leaveNode($node);
            if ($result !== null) {
                $node = $result;
            }
        }

        foreach ($this->visitors as $visitor) {
            $visitor->after($node);
        }

        return $node;
    }

    /**
     * @param array<Node> $children
     *
     * @return array<Node>
     */
    private function visitChildren(array $children): array
    {
        foreach ($children as &$child) {
            foreach ($this->visitors as $visitor) {
                $result = $visitor->enterNode($child);
                if ($result !== null) {
                    $child = $result;
                }

                if ($child instanceof NodeWithChildren) {
                    $child->setChildren($this->visitChildren($child->getChildren()));
                }

                $result = $visitor->leaveNode($child);
                if ($result === null) {
                    continue;
                }

                $child = $result;
            }
        }

        return $children;
    }
}
