<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\NodeVisitor;

use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\NodeVisitor;

class BaseNodeVisitor implements NodeVisitor
{
    public function before(Node $node): void
    {
        //noop
    }

    public function enterNode(Node $node): ?Node
    {
        return null;
    }

    public function leaveNode(Node $node): ?Node
    {
        return null;
    }

    public function after(Node $node): void
    {
        //noop
    }
}
