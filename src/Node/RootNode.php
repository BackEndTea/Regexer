<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;

use function array_map;
use function implode;

/**
 * The root node is the outer most node, describing the entire regex
 */
final class RootNode extends NodeWithChildren
{
    use WithChildren;

    private string $delimiter;
    private string $modifiers;

    /**
     * @param array<Node> $childNodes
     */
    public function __construct(
        string $delimiter,
        array $childNodes,
        string $modifiers
    ) {
        $this->delimiter = $delimiter;
        $this->modifiers = $modifiers;
        $this->children  = $childNodes;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    public function getModifiers(): string
    {
        return $this->modifiers;
    }

    public function setModifiers(string $modifiers): void
    {
        $this->modifiers = $modifiers;
    }

    public function asString(): string
    {
        return $this->delimiter
            . implode(
                '',
                array_map(
                    static fn (Node $node): string => $node->asString(),
                    $this->children
                )
            )
            . $this->delimiter
            . $this->modifiers;
    }
}
