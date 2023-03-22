<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node;

use BackEndTea\Regexer\Node;
use BackEndTea\Regexer\Util\QuantifierValidator;
use InvalidArgumentException;

use function array_key_first;
use function count;
use function sprintf;

final class Quantified extends NodeWithChildren
{
    public function __construct(private Node $quantifiedNode, private int $min, private int|null $max, private bool $lazy)
    {
    }

    public static function fromString(Node $quantifiedNode, string $quantifierString): self
    {
        [$min, $max] = QuantifierValidator::getMinAndMaxFromCharacters($quantifierString);

        return new self($quantifiedNode, $min, $max, false);
    }

    public function isLazy(): bool
    {
        return $this->lazy;
    }

    public function setLazy(bool $lazy): void
    {
        $this->lazy = $lazy;
    }

    public function getQuantifiedNode(): Node
    {
        return $this->quantifiedNode;
    }

    public function setQuantifiedNode(Node $quantifiedNode): void
    {
        $this->quantifiedNode = $quantifiedNode;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function setMin(int $min): void
    {
        $this->min = $min;
    }

    public function getMax(): int|null
    {
        return $this->max;
    }

    public function setMax(int|null $max): void
    {
        $this->max = $max;
    }

    public function asString(): string
    {
        return $this->quantifiedNode->asString() . $this->minAndMaxToString($this->min, $this->max) . ($this->lazy ? '?' : '');
    }

    private function minAndMaxToString(int $min, int|null $max): string
    {
        if ($min === 0) {
            if ($max === null) {
                return '*';
            }

            if ($max === 1) {
                return '?';
            }
        }

        if ($min === 1 && $max === null) {
            return '+';
        }

        if ($min === $max) {
            return sprintf('{%d}', $min);
        }

        if ($max === null) {
            return sprintf('{%d,}', $min);
        }

        return sprintf('{%d,%d}', $min, $max);
    }

    /** @inheritDoc */
    public function getChildren(): array
    {
        return [$this->getQuantifiedNode()];
    }

    /** @inheritDoc */
    public function setChildren(array $children): void
    {
        if (count($children) !== 1) {
            throw new InvalidArgumentException('Can only quantify a single child');
        }

        $this->quantifiedNode = $children[array_key_first($children)];
    }

    public function addChild(Node $node): void
    {
        throw new InvalidArgumentException('cant add child to quantified node');
    }
}
