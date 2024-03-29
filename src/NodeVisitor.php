<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

interface NodeVisitor
{
    public function before(Node $node): void;

    public function enterNode(Node $node): Node|null;

    public function leaveNode(Node $node): Node|null;

    public function after(Node $node): void;
}
