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
use function strlen;
use function substr;

final class TokenParser implements Parser
{
    public function __construct(private Lexer $lexer)
    {
    }

    public function parse(string $regex): Node
    {
        return $this->convert(
            Util::iterableToList($this->lexer->regexToTokenStream(
                new StringStream($regex),
            )),
        );
    }

    /**
     * @internal
     * This method is not part of the API, but used inside of tests.
     *
     * This method naively assumes the array of tokens is a valid regex.
     *
     * @param list<Token> $tokens
     */
    public function convert(array $tokens): Node
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

            $children = $root->getChildren();
            if (isset($children[count($children) - 1])) {
                $child = $children[count($children) - 1];
                if ($child instanceof Node\Or_) {
                    [$i] = $this->parseFromToken($child, $tokens, $i);
                    continue;
                }
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

    /** @return array{string, string} */
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

            if ($token instanceof Token\SubPattern\NonCapturing) {
                $pattern->setCapturing(false);
                continue;
            }

            if ($token instanceof Token\SubPattern\Named) {
                $pattern->setName(Node\SubPattern\Name::fromCharacters($token->asString()));
                continue;
            }

            $children = $pattern->getChildren();
            if (isset($children[count($children) - 1])) {
                $child = $children[count($children) - 1];
                if ($child instanceof Node\Or_) {
                    [$i] = $this->parseFromToken($child, $tokens, $i);
                    continue;
                }
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

        if ($token instanceof Token\LiteralCharacters) {
            $node = new Node\LiteralCharacters($token->asString());
        }

        if ($token instanceof Token\Escaped\EscapedCharacter) {
            $node = new Node\Escaped(substr($token->asString(), -1));
        }

        if ($token instanceof Token\SubPattern\Reference) {
            $tokenString = $token->asString();

            if ($tokenString[1] === 'k') {
                $node = new Node\SubPattern\Reference('\\k' . $tokenString[2], substr($token->asString(), 3, -1));
            } elseif ($tokenString[0] === '(') {
                $node = new Node\SubPattern\Reference('(?P=', substr($token->asString(), 4, -1));
            } elseif ($tokenString[1] !== 'g') {
                $node = new Node\SubPattern\Reference('\\', substr($token->asString(), 1));
            } elseif ($tokenString[2] === '{') {
                $node = new Node\SubPattern\Reference('\\g{', substr($token->asString(), 3, -1));
            } else {
                $node = new Node\SubPattern\Reference('\\g', substr($token->asString(), 2));
            }
        }

        if ($token instanceof Token\Dot) {
            $node = new Node\Dot();
        }

        if ($token instanceof Token\Anchor\End) {
            $node = new Node\Anchor\End();
        }

        if ($token instanceof Token\Anchor\Start) {
            $node = new Node\Anchor\Start();
        }

        if ($token instanceof Token\BracketList\Start) {
            [$i, $node] = $this->parseBracketList($tokens, $i + 1);
        }

        if ($token instanceof Token\SubPattern\Start) {
            [$i, $node] = $this->parseSubPattern($tokens, $i + 1);
        }

        if ($token instanceof Token\Or_) {
            if ($parent instanceof Node\Or_) {
                $child = $parent->getRight();
                $or    = new Node\Or_($child, new Node\NoopNode());
                $parent->setRight($or);
                [$i, $node] = $this->parseFromToken($or, $tokens, ++$i);

                $or->setRight($node);

                return [$i, $or];
            }

            $children = $parent->getChildren();
            $child    = count($children) === 1
                ? $children[0]
                : new Node\NodeGroup($children);
            $or       = new Node\Or_($child, new Node\NoopNode());

            $parent->setChildren([$or]);

            [$i, $node] = $this->parseFromToken($or, $tokens, ++$i);

            $or->setRight($node);

            return [$i, $or];
        }

        if ($token instanceof Token\Quantifier\QuantifierToken) {
            return $this->handleQuantifier($tokens, $parent, $token, $i);
        }

        if ($node instanceof Node) {
            $parent->addChild($node);

            return [$i, $node];
        }

        throw NotImplemented::fromToken($token);
    }

    /**
     * @param array<Token> $tokens
     *
     * @return array{int, Node}
     */
    private function handleQuantifier(array $tokens, Node\NodeWithChildren $parent, Token\Quantifier\QuantifierToken $token, int $i): array
    {
        if (! $parent instanceof Node\Or_) {
            return $this->quantifyWithChildren($tokens, $parent, $token, $i);
        }

        $last = $parent->getRight();
        if ($last instanceof Node\NodeGroup) {
            return $this->quantifyWithChildren($tokens, $last, $token, $i);
        }

        if (! $last instanceof Node\LiteralCharacters) {
            $parent->setRight($node = Node\Quantified::fromString($last, $token->asString()));
            if ($tokens[$i + 1] instanceof Token\Quantifier\Lazy) {
                $node->setLazy(true);
                $i++;
            }

            return [$i, $node];
        }

        $lastCharactes = $last->getCharacters();
        if (strlen($lastCharactes) <= 1) {
            $parent->setRight($node = Node\Quantified::fromString($last, $token->asString()));
            if ($tokens[$i + 1] instanceof Token\Quantifier\Lazy) {
                $node->setLazy(true);
                $i++;
            }

            return [$i, $node];
        }

        $last->setCharacters(substr($lastCharactes, 0, -1));
        $last = new Node\LiteralCharacters(substr($lastCharactes, -1));

        $parent->addChild($node = Node\Quantified::fromString($last, $token->asString()));
        if ($tokens[$i + 1] instanceof Token\Quantifier\Lazy) {
            $node->setLazy(true);
            $i++;
        }

        return [$i, $node];
    }

    /**
     * @param array<Token> $tokens
     *
     * @return array{int, Node}
     */
    private function quantifyWithChildren(array $tokens, Node\NodeWithChildren $parent, Token\Quantifier\QuantifierToken $token, int $i): array
    {
        $children = $parent->getChildren();
        $last     = array_pop($children);
        if ($last === null) {
            throw new LogicException('should not happen');
        }

        if ($last instanceof Node\LiteralCharacters) {
            $lastCharactes = $last->getCharacters();
            if (strlen($lastCharactes) > 1) {
                $last->setCharacters(substr($lastCharactes, 0, -1));
                $children[] = $last;
            }

            $last = new Node\LiteralCharacters(substr($lastCharactes, -1));
        }

        $children[] = $node = Node\Quantified::fromString($last, $token->asString());

        $parent->setChildren($children);

        if ($tokens[$i + 1] instanceof Token\Quantifier\Lazy) {
            $node->setLazy(true);
            $i++;
        }

        return [$i, $node];
    }
}
