<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Token\Quantifier\QuantifierToken;

use function count;
use function explode;
use function trim;

final class Quantified extends Node
{
    private Node $quantifiedNode;
    private string $characters;
    private int $min;
    private ?int $max;

    public function __construct(Node $quantifiedNode, string $characters, int $min, ?int $max)
    {
        $this->quantifiedNode = $quantifiedNode;
        $this->characters     = $characters;
        $this->min            = $min;
        $this->max            = $max;
    }

    public static function fromToken(Node $quantifiedNode, QuantifierToken $token): self
    {
        $asString = $token->asString();
        switch ($asString) {
            case '+':
                return new self($quantifiedNode, '+', 1, null);

            case '*':
                return new self($quantifiedNode, '*', 0, null);

            case '?':
                return new self($quantifiedNode, '?', 0, 1);
        }

        $pattern = trim($asString, '{}');
        $items   = explode(',', $pattern);

        if (count($items) === 1) {
            return new self($quantifiedNode, $asString, (int) $items[0], (int) $items[0]);
        }

        if ($items[1] === '') {
            return new self($quantifiedNode, $asString, (int) $items[0], null);
        }

        return new self($quantifiedNode, $asString, (int) $items[0], (int) $items[1]);
    }

    public function getQuantifiedNode(): Node
    {
        return $this->quantifiedNode;
    }

    public function setQuantifiedNode(Node $quantifiedNode): void
    {
        $this->quantifiedNode = $quantifiedNode;
    }

    public function getCharacters(): string
    {
        return $this->characters;
    }

    public function setCharacters(string $characters): void
    {
        $this->characters = $characters;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function setMin(int $min): void
    {
        $this->min = $min;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): void
    {
        $this->max = $max;
    }

    public function asString(): string
    {
        return $this->quantifiedNode . $this->characters;
    }
}
