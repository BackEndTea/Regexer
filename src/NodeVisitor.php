<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

interface NodeVisitor
{
    public function before(Node $node): void;

    public function enterNode(Node $node): ?Node;

    public function leaveNode(Node $node): ?Node;

    public function after(Node $node): void;
}
