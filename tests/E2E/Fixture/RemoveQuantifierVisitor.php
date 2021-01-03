<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\E2E\Fixture;

use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\NodeVisitor\BaseNodeVisitor;

final class RemoveQuantifierVisitor extends BaseNodeVisitor
{
    public function leaveNode(Node $node): ?Node
    {
        if ($node instanceof Node\Quantified) {
            return $node->getQuantifiedNode();
        }

        return null;
    }
}
