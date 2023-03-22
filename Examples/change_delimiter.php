<?php

namespace ChangeDelimiter;

require_once __DIR__ .'/../vendor/autoload.php';

use BackEndTea\Regexer\Lexer\Lexer;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\NodeVisitor\BaseNodeVisitor;
use BackEndTea\Regexer\Parser\TokenParser;
use BackEndTea\Regexer\Traverser;

class ChangeDelimiter extends BaseNodeVisitor
{
    public function enterNode(Node $node): ?Node
    {
        if ($node instanceof Node\RootNode) {
            $node->setDelimiter('(');
            return $node;
        }

        return null;
    }
}

$ast = (new TokenParser(new Lexer()))->parse('/((foo)|(bar)){2,4}/');

$newAst = (new Traverser([new ChangeDelimiter()]))->traverse($ast);

echo $newAst->asString();
$output = '(((foo)|(bar)){2,4})';
