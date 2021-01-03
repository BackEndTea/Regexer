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

            if ($token instanceof Token\BracketList\Start) {
                $i = $this->parseBracketList($root, $tokens, $i + 1);
                continue;
            }

            if ($token instanceof Token\SubPattern\Start) {
                $i = $this->parseSubPattern($root, $tokens, $i + 1);
                continue;
            }

            if ($token instanceof Token\Or_) {
                $i = $this->handleOr($root, $tokens, $i);
                continue;
            }

            if ($token instanceof Token\LiteralCharacters) {
                $root->addChild(new Node\LiteralCharacters($token->asString()));
                continue;
            }

            if ($token instanceof Token\Escaped\EscapedCharacter) {
                $root->addChild(new Node\Escaped(substr($token->asString(), -1)));
                continue;
            }

            if ($token instanceof Token\Modifier) {
                $root->setModifiers($token->asString());
                continue;
            }

            if ($token instanceof Token\Quantifier\QuantifierToken) {
                $children = $root->getChildren();
                $last     = array_pop($children);
                if ($last === null) {
                    throw new LogicException('should not happen');
                }

                $children[] = Node\Quantified::fromToken($last, $token);
                $root->setChildren($children);
                continue;
            }

            if ($token instanceof Token\Delimiter) {
                continue;
            }

            throw NotImplemented::fromToken($token);
        }

        return $root;
    }

    /**
     * A bracket list can only contain 'simple' children.
     *
     * @param array<Token> $tokens
     */
    private function parseBracketList(Node\NodeWithChildren $root, array $tokens, int $currentIndex): int
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

        $result = new Node\BracketList(
            $negated,
            $parts
        );
        if ($root instanceof Node\Or_) {
            $root->setRight($result);

            return $i;
        }

        $root->addChild($result);

        return $i;
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
     */
    private function parseSubPattern(Node\NodeWithChildren $root, array $tokens, int $currentIndex): int
    {
        $end     = count($tokens);
        $pattern = new Node\SubPattern([]);

        for ($i = $currentIndex; $i < $end; $i++) {
            $token = $tokens[$i];

            if ($token instanceof Token\SubPattern\End) {
                break;
            }

            if ($token instanceof Token\SubPattern\Start) {
                $i = $this->parseSubPattern($pattern, $tokens, $i + 1);
                continue;
            }

            if ($token instanceof Token\BracketList\Start) {
                $i = $this->parseBracketList($pattern, $tokens, $i + 1);
                continue;
            }

            if ($token instanceof Token\Or_) {
                $i = $this->handleOr($pattern, $tokens, $i);
                continue;
            }

            if ($token instanceof Token\LiteralCharacters) {
                $pattern->addChild(new Node\LiteralCharacters($token->asString()));
                continue;
            }

            if ($token instanceof Token\Escaped\EscapedCharacter) {
                $pattern->addChild(new Node\Escaped(substr($token->asString(), -1)));
                continue;
            }

            if ($token instanceof Token\Quantifier\QuantifierToken) {
                $children = $pattern->getChildren();
                $last     = array_pop($children);
                if ($last === null) {
                    throw new LogicException('should not happen');
                }

                $children[] = Node\Quantified::fromToken($last, $token);
                $pattern->setChildren($children);
                continue;
            }

            throw NotImplemented::fromToken($token);
        }

        if ($root instanceof Node\Or_) {
            $root->setRight($pattern);

            return $i;
        }

        $root->addChild($pattern);

        return $i;
    }

    /**
     * @param Token[] $tokens
     */
    private function parseOr(Node\NodeWithChildren $root, Node $left, array &$tokens, int $currentIndex): int
    {
        $or = new Node\Or_($left, new Node\NoopNode());
        $root->addChild($or);
        $currentIndex++;

        $token = $tokens[$currentIndex];

        if ($token instanceof Token\BracketList\Start) {
            return $this->parseBracketList($or, $tokens, $currentIndex + 1);
        }

        if ($token instanceof Token\SubPattern\Start) {
            return $this->parseSubPattern($or, $tokens, $currentIndex + 1);
        }

        if ($token instanceof Token\LiteralCharacters) {
            $or->setRight(new Node\LiteralCharacters($token->asString()));

            return $currentIndex;
        }

        throw NotImplemented::fromToken($token);
    }

    /**
     * @param Token[] $tokens
     */
    private function handleOr(Node\NodeWithChildren $parent, array &$tokens, int $i): int
    {
        $currentChildren = $parent->getChildren();
        $last            = array_pop($currentChildren);

        if ($last === null) {
            throw new LogicException('should not happen');
        }

        $parent->setChildren($currentChildren);

        return $this->parseOr($parent, $last, $tokens, $i);
    }
}
