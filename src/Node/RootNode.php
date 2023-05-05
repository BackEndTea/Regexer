<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token\Delimiter;

use function array_map;
use function implode;

/**
 * The root node is the outer most node, describing the entire regex
 */
final class RootNode extends NodeWithChildren
{
    use WithChildren;

    /** @param list<Node> $childNodes */
    public function __construct(
        private string $delimiter,
        array $childNodes,
        private string $modifiers,
    ) {
        $this->children = $childNodes;
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
                    $this->children,
                ),
            )
            . Delimiter::getClosingDelimiter($this->delimiter)
            . $this->modifiers;
    }
}
