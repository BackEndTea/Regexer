<?php

require_once __DIR__ .'/../vendor/autoload.php';

use BackEndTea\Regexer\Lexer\Lexer;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\NodeVisitor\BaseNodeVisitor;
use BackEndTea\Regexer\Parser\TokenParser;
use BackEndTea\Regexer\Traverser;

class RemoveQuantifierVisitor extends BaseNodeVisitor
{
    public function leaveNode(Node $node): ?Node
    {
        if ($node instanceof Node\Quantified) {
            return $node->getQuantifiedNode();
        }

        return null;
    }
}

$ast = (new TokenParser(new Lexer()))->parse('/((foo)|(bar)){2,4}/');

$newAst = (new Traverser([new RemoveQuantifierVisitor()]))->traverse($ast);

echo $newAst->asString(); // "/((foo)|(bar))/"
