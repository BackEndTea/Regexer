<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

use BackEndTea\Regexer\Node\NodeWithChildren;
use BackEndTea\Regexer\Node\NoopNode;

use function array_filter;
use function array_values;

final class Traverser
{
    /** @param list<NodeVisitor> $visitors */
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
     * @param list<Node> $children
     *
     * @return list<Node>
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

        return array_values(array_filter($children, static fn (Node $node) => ! $node instanceof NoopNode));
    }
}
