<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Parser;

use BackEndTea\Regexer\Lexer\Lexer;
use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\NotImplemented;
use BackEndTea\Regexer\StringStream;
use BackEndTea\Regexer\Token;
use BackEndTea\Regexer\Util\Util;
use LogicException;

use function array_pop;
use function assert;
use function count;
use function str_split;
use function substr;

final class TokenParser implements Parser
{
    private Lexer $lexer;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    public function parse(string $regex): Node
    {
        return $this->convert(
            Util::iterableToArray($this->lexer->regexToTokenStream(
                new StringStream($regex)
            ))
        );
    }

    /**
     * This method naively assumes the array of tokens is a valid regex.
     *
     * @param array<Token> $tokens
     */
    private function convert(array $tokens): Node
    {
        $delimiterToken = $tokens[0];

        assert($delimiterToken instanceof Token\Delimiter);
        $delimiter = $delimiterToken->asString();

        $root = new Node\RootNode($delimiter, [], '');

        $max = count($tokens);
        for ($i = 1; $i < $max; $i++) {
            $token = $tokens[$i];

            if ($token instanceof Token\Modifier) {
                $root->setModifiers($token->asString());
                break;
            }

            if ($token instanceof Token\Delimiter) {
                continue;
            }

            [$i] = $this->parseFromToken($root, $tokens, $i);
        }

        return $root;
    }

    /**
     * A bracket list can only contain 'simple' children.
     *
     * @param array<Token> $tokens
     *
     * @return array{int, Node}
     */
    private function parseBracketList(array $tokens, int $currentIndex): array
    {
        $negated = false;
        $parts   = [];
        $end     = count($tokens);
        for ($i = $currentIndex; $i < $end; $i++) {
            $token = $tokens[$i];
            if ($token instanceof Token\BracketList\End) {
                break;
            }

            if ($token instanceof Token\BracketList\Not) {
                $negated = true;
                continue;
            }

            if ($token instanceof Token\BracketList\Range) {
                $parts[] = new Node\BracketList\Range(...$this->getFromToOfRange($token));
                continue;
            }

            if ($token instanceof Token\BracketList\OneOf) {
                $parts[] = new Node\BracketList\OneOf($token->asString());
                continue;
            }

            if ($token instanceof Token\Escaped\EscapedCharacter) {
                $parts[] = new Node\Escaped(substr($token->asString(), -1));
                continue;
            }

            throw NotImplemented::fromToken($token);
        }

        return [$i, new Node\BracketList($negated, $parts)];
    }

    /**
     * @return array{string, string}
     */
    private function getFromToOfRange(Token\BracketList\Range $range): array
    {
        $parts = str_split($range->asString());
        if ($parts[0] === '\\') {
            $from = $parts[0] . $parts[1];
            $next = 3;
        } else {
            $from = $parts[0];
            $next = 2;
        }

        if ($parts[$next] === '\\') {
            $to = $parts[$next] . $parts[$next + 1];
        } else {
            $to = $parts[$next];
        }

        return [$from, $to];
    }

    /**
     * @param Token[] $tokens
     *
     * @return array{int, Node}
     */
    private function parseSubPattern(array $tokens, int $currentIndex): array
    {
        $end     = count($tokens);
        $pattern = new Node\SubPattern([]);

        for ($i = $currentIndex; $i < $end; $i++) {
            $token = $tokens[$i];

            if ($token instanceof Token\SubPattern\End) {
                break;
            }

            [$i] = $this->parseFromToken($pattern, $tokens, $i);
        }

        return [$i, $pattern];
    }

    /**
     * @param array<Token> $tokens
     *
     * @return array{int, Node}
     */
    private function parseFromToken(Node\NodeWithChildren $parent, array $tokens, int $i): array
    {
        $node  = null;
        $token = $tokens[$i];

        if ($token instanceof Token\BracketList\Start) {
            [$i, $node] = $this->parseBracketList($tokens, $i + 1);
        }

        if ($token instanceof Token\SubPattern\Start) {
            [$i, $node] = $this->parseSubPattern($tokens, $i + 1);
        }

        if ($token instanceof Token\Or_) {
            $children = $parent->getChildren();
            $child    = count($children) === 1
                ? $children[0]
                : new Node\NodeGroup($children);

            $or = new Node\Or_($child, new Node\NoopNode());

            $parent->setChildren([$or]);

            [$i, $node] = $this->parseFromToken($or, $tokens, ++$i);

            $or->setRight($node);

            return [$i, $node];
        }

        if ($token instanceof Token\LiteralCharacters) {
            $node = new Node\LiteralCharacters($token->asString());
        }

        if ($token instanceof Token\Escaped\EscapedCharacter) {
            $node = new Node\Escaped(substr($token->asString(), -1));
        }

        if ($token instanceof Token\Quantifier\QuantifierToken) {
            $children = $parent->getChildren();
            $last     = array_pop($children);
            if ($last === null) {
                throw new LogicException('should not happen');
            }

            $parent->setChildren($children);
            $node = Node\Quantified::fromToken($last, $token);
        }

        if ($token instanceof Token\Dot) {
            $node = new Node\Dot();
        }

        if ($node instanceof Node) {
            if ($parent instanceof Node\Or_) {
                $parent->setRight($node);
            } else {
                $parent->addChild($node);
            }

            return [$i, $node];
        }

        throw NotImplemented::fromToken($token);
    }
}
